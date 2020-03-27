<?php

namespace Unmit\ldk\Models;

use InvalidArgumentException;
use DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;


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
    protected $parsedStmt;
    private $bindings;

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
                //$this->pdo = DB::connection($this->getConnection())->getPdo();
            }
//            else
//            {
//                $this->pdo = DB::getPdo();
//            }
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
        $oracleConfig = config('oracle.'.$connection);

        $dbconnstr = '(DESCRIPTION = (ADDRESS_LIST =
                  (ADDRESS = (COMMUNITY = tcp)(PROTOCOL = TCP)(Host ='.$oracleConfig["host"].' )(Port = '.$oracleConfig["port"].')))
                      (CONNECT_DATA = (SID = '.$oracleConfig["database"].')(GLOBAL_NAME = '.$oracleConfig["database"].')))';
        $conn = oci_connect($oracleConfig["username"],$oracleConfig["password"],$dbconnstr);
        $this->connection = $conn;
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
        $rawStmt = (empty($this->getDeclarations())? null:self::SQL_DECLARE." ".$this->getDeclarations()->implode('') ).
            self::SQL_BEGIN." ".
            ($this->isSchemaDefined()? $this->getSchema().".": null).$this->getPackage().".".$procedure."(".
            $inputParameters->implode(',').");".
            self::SQL_END.";";
        if(!empty($rawStmt))
        {
            if($this->getConnection())
            {
                return oci_parse($this->getConnection(),$rawStmt);
            }
        }
        throw new Oci8Exception(__CLASS__.": Could not build a proper procedure call");
    }


    /**
     * @param array|null $expectedOutput
     * @param $procedure
     * @return boolean;
     */
    public function executePrep($procedure = null){
        $statement = null;

        if(!empty($procedure))
        {
            $statement = $this->getStatementString($procedure);
        }
        elseif(!empty($this->getProcedure()))
        {
            $statement = $this->getStatementString($this->getProcedure());
        }
        else
        {
            throw new Oci8Exception("procedure cannot be empty");
        }
        $this->parsedStmt = $statement;
        return true;
    }
    /**
     * Binds a parameter to the specified variable name.
     *
     * @param string $parameter Parameter identifier. For a prepared statement
     *   using named placeholders, this will be a parameter name of the form
     *   :name. For a prepared statement using question mark placeholders, this
     *   will be the 1-indexed position of the parameter.
     * @param mixed $variable Name of the PHP variable to bind to the SQL
     *   statement parameter.
     * @param int $dataType Explicit data type for the parameter using the
     *   PDO::PARAM_* constants.
     * @param int $maxLength Length of the data type. To indicate that a
     *   parameter is an OUT parameter from a stored procedure, you must
     *   explicitly set the length.
     * @param array $options [optional]
     * @return bool TRUE on success or FALSE on failure.

     */
    public function bindParam($parameter, &$variable, $dataType = PDO::PARAM_STR, $maxLength = -1, $ociType = SQLT_CHR)
    {
        if(isset($this->parsedStmt))
        {
            $this->bindings[] = &$variable;
            return oci_bind_by_name($this->parsedStmt, $parameter, $variable, $maxLength, $ociType);
        }
        throw new Oci8Exception(__CLASS__."-".__METHOD__.": Could not bind parameter because invalid statement used");
    }
    /**
     * Executes a prepared statement.
     *
     * @param array $inputParams An array of values with as many elements as
     *   there are bound parameters in the SQL statement being executed.
     * @throws Oci8Exception
     * @return bool TRUE on success or FALSE on failure
     */
    public function execute($inputParams = null)
    {
        $mode = OCI_COMMIT_ON_SUCCESS;

        $result = @oci_execute($this->parsedStmt, $mode);

        if ($result != true) {
            $e = oci_error($this->parsedStmt);

            $message = '';
            $message = $message . 'Error Code    : ' . $e['code'] . PHP_EOL;
            $message = $message . 'Error Message : ' . $e['message'] . PHP_EOL;
            $message = $message . 'Position      : ' . $e['offset'] . PHP_EOL;
            $message = $message . 'Statement     : ' . $e['sqltext'] . PHP_EOL;
            $message = $message . 'Bindings      : [' . $this->displayBindings() . ']' . PHP_EOL;

            throw new Oci8Exception($message, $e['code']);
        }

        return $result;
    }
    /**
     * Special oci function to format display of query bindings.
     *
     * @return string
     */
    private function displayBindings()
    {
        $bindings = [];
        foreach ($this->bindings as $binding) {
            if (is_object($binding)) {
                $bindings[] = get_class($binding);
            } elseif (is_array($binding)) {
                $bindings[] = 'Array';
            } else {
                $bindings[] = (string) $binding;
            }
        }

        return implode(',', $bindings);
    }
}
