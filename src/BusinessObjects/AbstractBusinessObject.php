<?php

namespace Unmit\ldk\BusinessObjects;

use http\Exception\InvalidArgumentException;
use Unmit\ldk\BusinessObjects\BusinessObjectInterface;
use Illuminate\Support\Collection;

/**
 * Class: AbstractBusinessObject
 *
 * MH = Michael Han <mhan1@unm.edu>
 * RVE = Ronald Estrada <rvestra@unm.edu>
 *
 * @see BusinessObjectInterface
 * @abstract
 *
 * @version 0.1.4 2019-11-18 MH
 *   0.1.3 2019-10-11 MH
 * @since 0.1.0
 */
abstract class AbstractBusinessObject implements BusinessObjectInterface
{
    /** @var Collection A collection of BusinessObjectItem */
    private $fields;

    /** @var array Direct access array:  name => colName */
    private $nameToColumnName = [];

    /** @var array Direct access array: name => bizName */
    private $nameToBusinessName = [];

    /** @var array Direct access array: colName => Name */
    private $columnNameToName = [];

    /**
     * @return array|mixed
     */
    public function getNameToColumnNameArray()
    {
        return $this->nameToColumnName;
    }

    /**
     * @return mixed
     */
    public function getNameToBusinessNameArray()
    {
        return $this->nameToBusinessName;
    }

    /**
     * @return array
     */
    public function getColumnNameToNameArray()
    {
        return $this->columnNameToName;
    }

    /**
     * Assign Laravel collection of BusinessObjectItems that define the API
     * Resource
     * @param Collection $fields
     *
     * @todo type checking: void
     */
    public function setFields(Collection $fields)
    {
        if(empty($fields))
        {
            throw new InvalidArgumentException('Business object requires items');
        }
        $this->fields = $fields;
    }

    /**
     * @return Collection
     *
     * @todo type checking
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get id column name of an object
     *
     */
    public function getIdColumnName()
    {
        $item = $this->getByName(BusinessObjectInterface::ID_NAME);
        if(empty($item)){
            throw new \Exception('Business Object does not have an id');
        }
        return $item->getColumnName();
    }

    /**
     * @return mixed
     *
     * @todo type checking: array
     */

    public function getNames()
    {
        return $this->getArray(BusinessObjectItem::NAME);
    }
    /**
     * @return mixed
     */
    public function getBusinessNames()
    {
        return $this->getArray(BusinessObjectItem::BUSINESS_NAME);
    }

    /**
     * @return mixed
     *
     * @todo type checking: array
     */
    public function getColumnNames()
    {
        return $this->getArray(BusinessObjectItem::COLUMN_NAME);
    }

    /**
     * @param string $column
     * @return BusinessObjectItem
     *
     * @version 0.1.2 MH
     * @since 0.1.0
     */
    public function getByColumnName($column)
    {
        $key = $this->getFields()->search(
            function ($item, $key) use ($column) {
                return $item->getColumnName() == $column;
            }
        );

        if ($key === false) return null;

        return $this->getFields()->get($key);
    }

    /**
     * @param $name
     * @return BusinessObjectItem
     *
     * @version 0.1.0 RVE
     * @since 0.1.0
     */
    public function getByName($name)
    {
        $key = $this->getFields()->search(
            function ($item, $key) use ($name) {
                return $item->getName() == $name;
            }
        );

        if ($key === false) return null;

        return $this->getFields()->get($key);
    }

    /**
     * @param $businessName
     * @return BusinessObjectItem
     *
     * @version 0.1.2 MH
     * @since 0.1.0
     */
    public function getByBusinessName($businessName)
    {
        $key = $this->getFields()->search(
            function ($item, $key) use ($businessName) {
                return $item->getBusinessName() == $businessName;
            }
        );

        if ($key === false) return null;

        return $this->getFields()->get($key);
    }

    /**
     * @param $name
     * @param $columnName
     * @return bool|null
     *
     * @version 0.1.0 RVE
     * @since 0.1.0
     */
    public function replaceColumnName($name = null, $columnName)
    {
        if ($name === null)
            return null;

        $key = $this->getFields()->search(
            function ($item, $key) use ($name) {
                return $item->getName() == $name;
            }
        );

        if ($key === false) return null;

        $item = $this->getFields()->get($key);
        $item->setColumnName($columnName);
        $this->getFields()->put($key, $item);

        return true;
    }


    /**
     * @param bool $useAliases
     * @return array|mixed
     */
    public function getSqlSelectItems($useAliases=true)
    {
        $selectItems = array();
        foreach ($this->getNameToColumnNameArray() as $name=>$column)
        {
            if($useAliases)
            {
                $selectItems[] .= $column.BusinessObjectItem::_AS_.$name;
            }
            else
            {
                $selectItems[] .= $column;
            }
        }
        return $selectItems;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getArray();
    }

    /**
     * @return mixed
     */
    public function toJson()
    {
        return json_encode($this->getArray(),JSON_FORCE_OBJECT);
    }

    /**
     * @param string $type [BUSINESS_NAME|COLUMN]
     *
     * @return array of business names, column names, or everything
     *
     * @version 0.1.0 MH
     * @since 0.1.0 2019-09-19
     */
    private function getArray($type = null) {
        // if $this->fields isn't set return null
        if (!isset($this->fields))
            return null;

        // set $list to an empty array
        $list = [];

        // loop through the collection of BusinessObjectItems
        $this->fields->each(function($item,$key) use ($type,&$list) {

            switch ($type) {

                // business names
                case BusinessObjectItem::BUSINESS_NAME:
                    $list[] = $item->getBusinessName();
                    break;

                // column names
                case BusinessObjectItem::COLUMN_NAME:
                    $list[] = $item->getColumnName();
                    break;

                // column names
                case BusinessObjectItem::NAME:
                    $list[] = $item->getName();
                    break;

                // all three
                default:
                    $list[] = [
                        BusinessObjectItem::BUSINESS_NAME => $item->getBusinessName(),
                        BusinessObjectItem::COLUMN_NAME => $item->getColumnName(),
                        'name' => $item->getName()
                    ];
            }

        });

        return $list;
    }


    /**
     * @author Michael Han <mhan1@unm.edu>, Ron Estrada <rvestra@unm.edu>
     * @version 0.1.3 MH - swap business_name & name
     *   0.1.2 MH - automatic business name uses underscore for spacing
     * @since 0.1.1
     * @param $items instantiated Collection; Likely the originating items class variable
     * @param $elems should be a two dimensional array containing key/val
     * with at least BUSINESS_NAME => 'element name'; if key is not defined,
     * it will just use modified name and same rule applies for COLUMN_NAME.
     *
     * keys as defined by BusinessObjectItem constants
     * BusinessObjectItem::BUSINESS_NAME - label, or name used for users
     * BusinessObjectItem::NAME - canonical identifier, or name as keys in code
     * BusinessObjectItem::COLUMN_NAME - column name in the database
     *
     * This will throw an exception if name is undefined and return false
     * if $elems is empty.
     * @param null $table_name the name of the table to use for prefixing; if empty
     * no prefixing will occur
     * @return bool
     */
    public function pushElements($items, $elems, $table_name = null)
    {
        // return false if there's nothing in $elems
        if (empty($elems)) return false;

        try {
            foreach ($elems as $o) {
                // instantiate business object
                $obj = new BusinessObjectItem();

                // throw up if name isn't defined
                if (!isset($o[BusinessObjectItem::BUSINESS_NAME]))
                    throw new \Exception('Object name must be defined.');

                $obj->setBusinessName($bizname = $o[BusinessObjectItem::BUSINESS_NAME]);

                // if [id] name isn't defined, then just replace spaces
                //   with dashes in biz-name
                if (array_key_exists(BusinessObjectItem::NAME,$o) && !empty($o[BusinessObjectItem::NAME])) {
                    $name = $o[BusinessObjectItem::NAME];
                } else {
                    $name = str_replace(' ','_',strtolower($o[BusinessObjectItem::BUSINESS_NAME]));
                }
                $obj->setName($name);

                // if colname isn't defined, then just replace spaces
                //   with underscores in name
                // @ todo: move prefixing to class method; also use table class variable in construction from Model
                $colname = isset($table_name) ? $table_name.'_':'';
                if (array_key_exists(BusinessObjectItem::COLUMN_NAME,$o) && !empty($o[BusinessObjectItem::COLUMN_NAME])) {
                    $colname .= $o[BusinessObjectItem::COLUMN_NAME];
                } else {
                    $colname .= str_replace(' ','_',strtolower($o[BusinessObjectItem::BUSINESS_NAME]));
                }
                $obj->setColumnName($colname);

                // set direct access arrays
                $this->nameToColumnName[$name] = $colname;
                $this->nameToBusinessName[$name] = $o[BusinessObjectItem::BUSINESS_NAME];
                $this->columnNameToName[$colname] = $name;

                // push it!
                $items->push($obj);
            }
        } catch (\Exception $e) {
            echo 'Exception occurred in '.__FUNCTION__.' on line '.$e->getLine().': '.$e->getMessage();
        }
    }

    /**
     * @param $name
     * @param $colname
     */
    public function setDirectAccessNameToColumn($name, $colname)
    {
        // set direct access arrays
        $this->nameToColumnName[$name] = $colname;
    }

    /**
     * @param $name
     * @param $bizname
     */
    public function setDirectAccessNameToBusinessName($name, $bizname)
    {
        // set direct access arrays
        $this->nameToBusinessName[$name] = $bizname;
    }

    /**
     * @param $colname
     * @param $name
     */
    public function setDirectAccessColumnNameToName($colname, $name)
    {
        // set direct access arrays
        $this->columnNameToName[$colname] = $name;
    }
}
