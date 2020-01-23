<?php

namespace Unmit\ldk\Models;

use InvalidArgumentException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\MassAssignmentException;
use DB;
use PDOException;
use PDOStatement;
use Unmit\ldk\BusinessObjects\BusinessObjectInterface;
use JsonSerializable;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Concerns\HidesAttributes;
use Illuminate\Database\Eloquent\Concerns\GuardsAttributes;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;

class EnterprisePackageBaseModel implements Arrayable, Jsonable, JsonSerializable
{
    use DBConstructionTrait;
    use HasAttributes,
        HidesAttributes,
        GuardsAttributes,
        HasRelationships;

    // Laravel requires CREATED_AT for the model traits
    const CREATED_AT = null;
    const UPDATED_AT = null;
    public $timestamps = false;


    /*
     * The package prefix is necessary to map business object column names to a package
     * and its attributes
     */
    const PARAM_PREFIX = ':';

    //PL/SQL Package Syntax reserved words
    const SQL_DECLARE = 'DECLARE';
    const SQL_BEGIN = 'BEGIN';
    const SQL_END = 'END';

    // Laravel set property to satisfy model traits
//    private $relations = array();

    // PDO Object is a lean, consistent way to access the databases
    private $pdo;

    // Declarations are a list of declared varables for a sql package call
    public $declarations;

    // Defaults are a unprepared key = value paired list of inputs appended to the parameters list
    public $defaults;
    private $sequence;

    // Database package
    private $package;

    // Database
    private $procedure;
    private $connection;

    public function __construct($schema = null, $connection = null)
    {
        if(!empty($schema))
        {
            $this->setSchema($schema);
        }
        if('testing' != $connection)
        {
            if(!empty($connection))
            {
                $this->setConnection($connection);
                $this->pdo = DB::connection($this->getConnection())->getPdo();
            }
            else
            {
                $this->pdo = DB::getPdo();
            }
        }

    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection): void
    {
        $this->connection = $connection;
    }
    /**
     * @return mixed
     */
    public function getPackage()
    {
        if(empty($this->package))
            throw new InvalidArgumentException(__CLASS__.": Package cannot be null");
        return $this->package;
    }

    /**
     * @param mixed $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

    /**
     * @return mixed
     */
    public function getProcedure()
    {
        return $this->procedure;
    }

    /**
     * Set the procedure in order to inform the execute method.  If
     * procedure is not set, the execute method will take procedure as an argument.
     *
     * @param mixed $procedure
     */
    public function setProcedure($procedure)
    {
        // todo: validate the procedure is alphanumeric
        $this->procedure = $procedure;
    }

    /**
     * @return mixed
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param mixed $sequence
     */
    public function setSequence($sequence): void
    {
        $this->sequence = $sequence;
    }

    /**
     * @return null
     */
    public function getDeclarations()
    {
        if(empty($this->declarations))
        {
            return null;
        }
        return $this->declarations->mapWithKeys(function ($input,$key) {
            return [$key => $key." ".$input.';'];
        });
    }

    /**
     *
     *
     * @param null $declarable
     */
    public function setDeclarations(array $declarables)
    {
        $this->declarations = collect($declarables);
    }

    /**
     * Should resolve your param => type array as collection of strings in the following format:
     * [PARAM TYPE;]
     *
     * @return mixed
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Must pass an array of key value pairs
     * ['key' => 'value']
     * key = ':<parameter>'
     * value = <default>
     *
     * @param mixed $defaults
     */
    public function setDefaults(array $defaults): void
    {
        if(!is_array($defaults))
            throw new InvalidArgumentException(__CLASS__."Defaults must be an array!");
        $this->defaults = collect($defaults);
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getParameterList()
    {
        $sequenced = collect($this->getSequence());

        // Merge any Defaults
        if(!empty($this->getDefaults()))
        {
            $sequenced = $sequenced->merge($this->getDefaults()->mapWithKeys(function ($input,$key) {
                return [$key => $key];
            }));
        }
        // append the ':' prefix
        $params = collect([]);
        $sequenced->each(function ($input) use ($params){
            return $params->put($input, self::PARAM_PREFIX.$input);
        });
        return $params;
    }    /**
 * @return \Illuminate\Support\Collection
 */
    public function getBindings()
    {
        $bindings = collect([]);
        // Gather non hidden fillables
        collect($this->fillable)->reject(function ($input) {
            return in_array($input,$this->getHidden());
        })->each(function ($input,$key) use ($bindings){
            return $bindings->put($input,$this->getAttribute($input));
        });
//        if(!empty($this->getDeclarations()))
//        {
//            $this->getDeclarations()->each(function ($input,$key) use ($bindings){
//                return $bindings->put($key, self::PARAM_PREFIX.$key);
//            });
//        }

        return $bindings->toArray();
    }

    /**
     *  When using only the id, fillable will not be part of the parameter list
     * this usually needs to be left out.  This method will remove the fillable column name
     * from the fillable list and add it to them hidden list.
     */
    public function hideAllExceptId()
    {
        $hiding = $this->getFillable();
        // hide all attributes
        $pos = array_search($this->getBusinessObject()->getIdColumnName(), $hiding);

        // look for id
        unset($hiding[$pos]);
        // remove id from hidden attributes
        $this->setHidden($hiding);

    }

    /**
     * @param $procedure
     * @return string
     */
    public function getStatementString($procedure)
    {
        $inputParameters = $this->getParameterList();
        // Merge declarables
        if(!empty($this->getDeclarations()))
        {
            $this->getDeclarations()->each(function ($input,$key) use ($inputParameters){
                return $inputParameters->put($key, self::PARAM_PREFIX.$key);
            });
        }


        // DECLARE
        //      <outputs>;
        // BEGIN
        //      <schema>.<package>.<procedure>(
        //          <parameters>);
        // END;
        return (empty($this->getDeclarations())? null:self::SQL_DECLARE." ".$this->getDeclarations()->implode('') ).
            self::SQL_BEGIN." ".
            ($this->isSchemaDefined()? $this->getSchema().".": null).$this->getPackage().".".$procedure."(".
            $inputParameters->implode(',').");".
            self::SQL_END.";";
    }

    /**
     * @param $procedure
     * @return mixed
     */
    protected function preparePDO($procedure){
        $pdoStatementString = $this->getStatementString($procedure);
        try
        {
            return $this->pdo->prepare($pdoStatementString);
        }
        catch (PDOException $pdoe)
        {
            throw new PDOException($pdoe->getMessage());
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param PDOStatement $statement
     * @return PDOStatement
     */
//    protected function bindParameters(PDOStatement $statement){
//        // Read parameters and for each match in fillable bind
//        $this->getParameterList()->each(function ($param, $key)  use ($statement)
//        {
//            $value = $this->getAttribute($key);
//            $statement->bindParam($param,$value, \PDO::ATTR_EMULATE_PREPARES);
//        });
//        return $statement;
//
//    }

    /**
     * @param array|null $expectedOutput
     * @param $procedure
     * @return $this
     */
    public function executePrep($procedure = null){
        $statement = null;

        if(!empty($procedure))
        {
            $statement = $this->preparePDO($procedure);
        }
        elseif(!empty($this->getProcedure()))
        {
            $statement = $this->preparePDO($this->getProcedure());
        }
        else
        {
            throw new PDOException("procedure cannot be empty");
        }
        return $statement;
//        try
//        {
//            return $this->bindParameters($statement);
//        }
//        catch (PDOException $pdoe)
//        {
//            throw new PDOException($pdoe->getMessage());
//        }
//        catch(Exception $e)
//        {
//            throw new Exception($e->getMessage());
//        }
    }

    /*
     * Below are copied methods from Laravel Model Traits to allow this object to
     * function like a model like class
     */

    /**
     * This method was copied from:
     * Illuminate\Database\Eloquent\Model as of v5.8.*
     * Fill the package model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        $totallyGuarded = $this->totallyGuarded();

        foreach ($this->fillableFromArray($attributes) as $key => $value) {

            // The developers may choose to place some attributes in the "fillable" array
            // which means only those attributes may be set through mass assignment to
            // the model, and all others will just get ignored for security reasons.
            if ($this->isFillable($key)) {

                $this->setAttribute($key, $value);
            } elseif ($totallyGuarded) {
                throw new MassAssignmentException(sprintf(
                    'Add [%s] to fillable property to allow mass assignment on [%s].',
                    $key, get_class($this)
                ));
            }
        }
        return $this;
    }

    /**
     * Take the defined declared output parameters for the procedure and
     * iterate through each and set the corresponding attribute.
     * NOTE: the output parameter will need to be part of the composite
     * BusinessObjectInterface or fillable
     * @return bool
     */
    public function hydrateResults()
    {
        foreach($this->getDeclarations() as $attribute=>$type)
        {
            if($this->getFillable($attribute))
            {
                $this->setAttribute($attribute,$this->$attribute);
            }
        }
        return true;
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->attributesToArray(), $this->relationsToArray());
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * This method was copied from:
     * Illuminate\Database\Eloquent\Concerns\HasTimestamps as of v5.8.*
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps()
    {
        //return $this->timestamps; -- Removed for UNM
        return false;
    }

    /**
     * This method was copied from:
     * Illuminate\Database\Eloquent\Model as of v5.8.*
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        //return $this->incrementing;  -- Removed for UNM
        return false;
    }

    /**
     * This method was copied from:
     * Illuminate\Database\Eloquent\Concerns\HasRelationships
     * if the given relation is loaded.
     *
     * @param  string  $key
     * @return bool
     */
//    public function relationLoaded($key)
//    {
//        //return array_key_exists($key, $this->relations);
//        return false;
//    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}
