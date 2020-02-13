<?php

namespace Unmit\ldk\Models;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Unmit\ldk\BusinessObjects\BusinessObjectInterface;

/**
 * Class EnterpriseBaseModel
 * @author Ron Estrada <rvestra@unm.edu>
 * @auther Michael Han <mhan1@unm.edu>
 * @package Unmit\ldk\Models
 */
class EnterpriseBaseModel extends Model
{
    use DBConstructionTrait;
    /**
     * @var string PUT to reference http request type
     */
    const PUT = 'PUT';
    /**
     * @var string PATCH to reference http request type
     */
    const PATCH = 'PATCH';

    //Overrides Eloquent stuff
    /**
     * @var bool
     */
    public $incrementing = false;

    //Disables the Eloquent use of UPDATED_AT and CREATED_AT
    //TODO: override Illuminate DB timestamp methods to utilize *_activity_date
    //in banner tables
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * EnterpriseBaseModel constructor.
     * @param BusinessObjectInterface|null $businessObject
     * @param null $connection
     */
    public function __construct(BusinessObjectInterface $businessObject = null, $connection = null)
    {
        if(!empty($connection))
        {
            $this->connection = $connection;
        }

        $this->setBusinessObject($businessObject);
        if($this->getBusinessObject()->getIdColumnName() !== BusinessObjectInterface::ID_NAME){
            $this->primaryKey = $this->getBusinessObject()->getIdColumnName();
        }
    }

    /**
     * @param BusinessObjectInterface $businessObject
     * @return bool
     */
    public function setBusinessObject(BusinessObjectInterface $businessObject)
    {
        if(empty($businessObject))
        {
            throw new InvalidArgumentException('Business Object cannot be empty');
        }
        $this->businessObject = $businessObject;

        // this was inserted to prevent error during composer update
        // 2019-11-12 Michael Han <mhan1@unm.edu>
        // @todo revisit
        if (!is_null($this->getBusinessObject()->getColumnNames()))
            $this->fillable($this->getBusinessObject()->getColumnNames());

        return true;
    }

    /**
     * @param $query
     * @param $searchstr name/value pair of URL search params
     * @return mixed
     */
    public function scopeSearch($query, $searchstr = '')
    {
        if (trim($searchstr)=='')
            return $query;

        // TODO: add more parameters here & below (e.g. sort, orderby, etc)
        // set whereColumn parameter

        $whereColumn = $whereRaw = [];
        $whereStr = '';
        $firstfound = false;

        $columnNames = $this->getBusinessObject()->getNameToColumnNameArray();
        // for every column search for the search string
        foreach ($columnNames as $columnName) {
            if ($firstfound) {
                $whereStr .= ' or ';
            }
            $whereStr .= "UPPER({$columnName}) like ?";
            $whereColumn[] = '%'.strtoupper($searchstr).'%';
            $firstfound = true;
        }

        $whereRaw[] = $whereStr;
        $whereRaw[] = $whereColumn;

        return $query->when($whereRaw,function($query,$whereRaw)
        {
            return $query->whereRaw($whereRaw[0],$whereRaw[1]);
        }
        );
    }
    /**
     * Toggle from Column Name -> Name
     *
     * @param array $columns
     * @return array
     */
    public function translateToName()
    {
        $nameMap = $this->getBusinessObject()->getNameToColumnNameArray();
        $translation = array();
        foreach($nameMap as $name => $column) {
                $translation[$name] = $this->getAttribute($column);
        }
        return $translation;
    }
    /**
     * Set from Name -> Column Name
     *
     * @param array $values usually from request->all()
     * @return Model
     */
    public function translateToColumn($values)
    {
        $nameMap = $this->getBusinessObject()->getNameToColumnNameArray();
        foreach ($values as $name => $value) {
            $this->setAttribute($nameMap[$name], $value);
            unset($values[$name]);
        }
        return $this;
    }
}
