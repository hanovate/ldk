<?php

namespace Tests\Unit;

use Unmit\BusinessObjects\Student\Banner\DualCredit\InstitutionAgreement;
use Unmit\BusinessObjects\BusinessObjectItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class: BusinessObjectItemTest
 *
 * Uses Unmit\BusinessObjects\Student\Banner\DualCredit\InstitutionAgreement
 * for testing this class
 *
 * @see TestCase
 */
class BusinessObjectItemTest extends TestCase
{
    protected $obj;

    function __construct()
    {
        $this->obj = new InstitutionAgreement();
        parent::__construct();
    }

    public function testPrerequisites()
    {
        $this->assertTrue(class_exists('Unmit\BusinessObjects\Student\Banner\DualCredit\InstitutionAgreement'));
    }

    public function testBusinessObjectItem()
    {
        $name = 'school code';
        $bizname = 'hs-code';
        $columnname = 'szvdist_sbgi_code';

        $obj = new BusinessObjectItem($name,$bizname,$columnname);

        $this->assertTrue($obj->getName() == $name);
        $this->assertTrue($obj->getBusinessName() == $bizname);
        $this->assertTrue($obj->getColumnName() == $columnname);
    }

    public function testReplaceColumnName()
    {
        $name = 'high school code';
        $newColumnname = 'v_sbgi_code';

        $this->obj->replaceColumnName($name, $newColumnname);

        $this->assertTrue($this->obj->getByName($name)->getColumnName() == $newColumnname);
    }

    public function testBusinessObjectItemMethodsViaChild()
    {

        // for set & getName()
        $name = 'school code';
        // setName($name)
        $this->obj->getByBusinessName('hs-code')->setName($name);
        // getName()
        $this->assertTrue($this->obj->getByBusinessName('hs-code')->getName() == $name);


        // for set & getColumnName()
        $colname = 'szvdist_sbgi_code1';
        // setColumn($column)
        $this->obj->getByBusinessName('hs-code')->setColumnName($colname);
        // getColumn()
        $this->assertTrue($this->obj->getByBusinessName('hs-code')->getColumnName() == $colname);

        // for set & getBusinessName()
        $bizname = 'hs-code-1';
        // setBusinessName($businessName)
        $this->obj->getByColumnName($colname)->setBusinessName($bizname);
        // getBusinessName()
        $this->assertTrue($this->obj->getByColumnName($colname)->getBusinessName() == $bizname);
    }

}
