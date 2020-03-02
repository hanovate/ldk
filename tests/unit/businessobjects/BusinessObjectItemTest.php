<?php

namespace Unmit\ldk\tests\unit\businessobjects;

use Unmit\ldk\tests\unit\TestBO;
use Unmit\ldk\BusinessObjects\BusinessObjectItem;
use Unmit\ldk\tests\TestCase;

/**
 * Class: BusinessObjectItemTest
 *
 *
 * @see TestCase
 */
class BusinessObjectItemTest extends TestCase
{
    protected $obj;

    function __construct()
    {
        $this->obj = new TestBO();
        parent::__construct();
    }

    public function testPrerequisites()
    {
        $this->assertTrue(class_exists('Unmit\ldk\tests\Unit\TestBO'));
    }

    public function testBusinessObjectItem()
    {
        $name = 'title';
        $bizname = 'Title';
        $columnname = 'UNMTBL_TITLE';

        $obj = new BusinessObjectItem($name,$bizname,$columnname);

        $this->assertTrue($obj->getName() == $name);
        $this->assertTrue($obj->getBusinessName() == $bizname);
        $this->assertTrue($obj->getColumnName() == $columnname);
    }

    public function testReplaceColumnName()
    {
        $name = 'title';
        $newColumnname = 'UNMTBL_TITLE';

        $this->obj->replaceColumnName($name, $newColumnname);

        $this->assertTrue($this->obj->getByName($name)->getColumnName() == $newColumnname);
    }

    public function testBusinessObjectItemMethodsViaChild()
    {

        // for set & getName()
        $name = 'title';
        // setName($name)
        $this->obj->getByBusinessName('Title')->setName($name);
        // getName()
        $this->assertTrue($this->obj->getByBusinessName('Title')->getName() == $name);


        // for set & getColumnName()
        $colname = 'UNMTBL_TITLE';
        // setColumn($column)
        $this->obj->getByBusinessName('Title')->setColumnName($colname);
        // getColumn()
        $this->assertTrue($this->obj->getByBusinessName('Title')->getColumnName() == $colname);

        // for set & getBusinessName()
        $bizname = 'Title';
        // setBusinessName($businessName)
        $this->obj->getByColumnName($colname)->setBusinessName($bizname);
        // getBusinessName()
        $this->assertTrue($this->obj->getByColumnName($colname)->getBusinessName() == $bizname);
    }

}
