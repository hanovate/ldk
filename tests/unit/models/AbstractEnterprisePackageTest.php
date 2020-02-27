<?php

namespace Unmit\ldk\tests\unit\models;

use Unmit\ldk\tests\TestCase;
use Unmit\ldk\Models\EnterprisePackageBaseModel;


/**
 * Class: AbstractBusinessObjectItem
 *
 *
 *
 * @see TestCase
 */
class AbstractEnterprisePackageTest extends TestCase
{
    protected $pckgModel;
    protected $bo;

    function __construct()
    {
        $this->pckgModel = new EnterprisePackageBaseModel('schemastring','testing');
        $this->pckgModel->setPackage('TESTPCKG');
        parent::__construct();
    }

    public function testPrerequisites()
    {
        $this->assertTrue(class_exists('Unmit\ldk\Models\EnterprisePackageBaseModel'));
    }

    public function test_Construction()
    {
        $this->assertObjectHasAttribute('declarations', $this->pckgModel);
        $this->assertObjectHasAttribute('package', $this->pckgModel);
        $this->assertObjectHasAttribute('procedure', $this->pckgModel);
        $this->assertEquals('schemastring', $this->pckgModel->getSchema());
        $this->assertEquals('testing', $this->pckgModel->getConnection());
        $this->assertEquals('TESTPCKG', $this->pckgModel->getPackage());
    }

//    public function testGetParameters()
//    {
//        $defaults = ['V_VAL'=> 'valOut','V_VAL1'=> 'valOut1'];
//        $this->pckgModel->setDeclarations($defaults);
//        $this->assertEqualsCanonicalizing( [
//            "ID" => ":ID",
//            "TABLE_TITLE" => ":TABLE_TITLE",
//            "V_VAL" => ":V_VAL",
//            "V_VAL1" => ":V_VAL1"
//            ], $this->pckgModel->getParameterList()->toArray());
//
//    }

//    public function testGetDeclarations()
//    {
//        $declarables = ['V_SUB_DATE_OUT' => 'DATE',
//            'V_INIT_ID_OUT' => 'VARCHAR2(20)'];
//        $this->pckgModel->setDeclarations($declarables);
//        $this->assertEqualsCanonicalizing( ["V_SUB_DATE_OUT"  => "V_SUB_DATE_OUT DATE;",
//            "V_INIT_ID_OUT" =>  "V_INIT_ID_OUT VARCHAR2(20);"]
//            ,$this->pckgModel->getDeclarations()->toArray());
//
//    }
//
//    public function testProcedureStatementWithoutDeclarations()
//    {
//        $this->assertEquals("BEGIN schemastring.TESTPCKG.test_proc(:ID,:TABLE_TITLE);END;",
//            $this->pckgModel->getStatementString('test_proc'));
//    }
//    public function testProcedureStatementWithDeclarations()
//    {
//        $declarables = ['V_VAL' => 'TYPE(24)'];
//        $this->pckgModel->setDeclarations($declarables);
//        $this->assertEquals("DECLARE V_VAL TYPE(24);BEGIN schemastring.TESTPCKG.test_proc(:ID,:TABLE_TITLE,:V_VAL);END;",
//            $this->pckgModel->getStatementString('test_proc'));
//    }
//public function testProcedureStatementWithDefaults()
//{
//    $defaults = ['V_VAL'=> 'valDflt'];
//    $this->pckgModel->setDeclarations($defaults);
//    $this->assertEquals("BEGIN schemastring.TESTPCKG.test_proc(:ID,:TABLE_TITLE,:V_VAL);END;",
//        $this->pckgModel->getStatementString('test_proc'));
//}
//public function testProcedureStatementWithDefaultsAndDeclares()
//{
//    $defaults = ['DFLT_VAL'=> 'valDflt'];
//    $this->pckgModel->setDeclarations($defaults);
//    $declarables = ['DEC_VAL' => 'TYPE(00)'];
//    $this->pckgModel->setDeclarations($declarables);
//    $this->assertEquals("DECLARE DEC_VAL TYPE(00);BEGIN schemastring.TESTPCKG.test_proc(:ID,:TABLE_TITLE,:DFLT_VAL,:DEC_VAL);END;",
//        $this->pckgModel->getStatementString('test_proc'));
//}
//
//public function testProcedureStatementWithDeclares()
//{
//    $declarables = ['V_SUB_DATE_OUT' => 'DATE',
//        'V_INIT_ID_OUT' => 'VARCHAR2(20)',
//        'V_ACTV_OUT' => 'VARCHAR2(20)',
//        'V_TITLE_OUT' => 'VARCHAR2(35)'];
//    $this->pckgModel->setDeclarations($declarables);
//    $this->pckgModel->setHidden(['TABLE_TITLE']);
//    $this->assertEquals("DECLARE V_SUB_DATE_OUT DATE;V_INIT_ID_OUT VARCHAR2(20);V_ACTV_OUT VARCHAR2(20);V_TITLE_OUT VARCHAR2(35);BEGIN schemastring.TESTPCKG.test_proc(:ID,:V_SUB_DATE_OUT,:V_INIT_ID_OUT,:V_ACTV_OUT,:V_TITLE_OUT);END;",
//        $this->pckgModel->getStatementString('test_proc'));
//}



}
