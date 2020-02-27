<?php

namespace Unmit\ldk\Models;

use InvalidArgumentException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use DB;
use PDOException;
use Unmit\ldk\BusinessObjects\BusinessObjectInterface;
use JsonSerializable;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Concerns\HidesAttributes;
use Illuminate\Database\Eloquent\Concerns\GuardsAttributes;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;

class EnterprisePackageBaseModel
{
    use DBConstructionTrait;

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
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }
    /**
     * @return mixed
     */
    public function getPackage()
    {
        if(empty($this->package))
            throw new InvalidArgumentException(__CLASS__.": Package is null");
        return $this->package;
    }

    /**
     * @param mixed $package
     */
    public function setPackage($package)
    {
        if(empty($package))
            throw new InvalidArgumentException(__CLASS__.": Package cannot be null");
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
    public function setSequence($sequence)
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
     * @return \Illuminate\Support\Collection
     */
    public function getParameterList()
    {
        $sequenced = collect($this->getSequence());

        // append the ':' prefix
        $params = collect([]);
        $sequenced->each(function ($input) use ($params){
            return $params->put($input, self::PARAM_PREFIX.$input);
        });
        return $params;
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
    }

}
