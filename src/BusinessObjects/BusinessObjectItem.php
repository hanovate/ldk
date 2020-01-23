<?php
namespace Unmit\ldk\BusinessObjects;

/**
 * Class: BusinessObjectItem
 *
 * @version 0.1.0 2019-09-19 MH
 * @since 0.1.0
 */
class BusinessObjectItem
{
    // string constants used to isolate or set focus on an attribute
    const NAME = 'name';
    const BUSINESS_NAME = 'business-name';
    const COLUMN_NAME = 'column-name';

    /**
     * The attribute is used to define the item to the API program
     * example) netid
     *          department-code
     * @var string the descriptive name
     */
    private $name;

    /**
     * The front end user will see this value; UI Visible Label
     * This should be human readable
     * example) Netid
     *          Department Code
     * @var string Business name is the visible (Label) to the end user
     */
    private $businessName;

    /**
     * The field to put the backend target mapping
     * for instance:
     * example)
     * Table=> xxxx_column_name
     * Package=> p_parameter_name
     * @var string he field to put the backend target mapping
     */
    private $column;

    /**
     * @param string|array $name this can also be received as an array containg
     *   all of the rest of the variables in the following format:
     *  [ 'name' => 'hs-code',
     *    'column-name' => 'szvdist_sbgi_code',
     *    'business-name' => 'High School Code' ]
     * @param string $businessName
     * @param string $column
     */
    function __construct($name = null, $businessName = null, $column = null) {
        // if the first parameter is an array, then process accordingly
        if (is_array($name)) {
            // make assignments depending on the key
            foreach ($name as $k=>$v) {
                if (strtoupper($k) == strtoupper(self::NAME)) {
                    $this->setName($v);
                } elseif (strtoupper($k) == strtoupper(self::BUSINESS_NAME)) {
                    $this->setBusinessName($v);
                } elseif (strtoupper($k) == strtoupper(self::COLUMN_NAME)) {
                    $this->setColumnName($v);
                }
            }

        } else {
            // make assignments
            $this->setName($name);
            $this->setBusinessName($businessName);
            $this->setColumnName($column);
        }
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $column
     */
    public function setColumnName($column): void
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->column;
    }

    /**
     * @param string $businessName
     */
    public function setBusinessName($businessName): void
    {
        $this->businessName = $businessName;
    }

    /**
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

}
