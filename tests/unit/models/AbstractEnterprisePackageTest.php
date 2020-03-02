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

    protected $defaults = ['V_VAL'=> 'valOut','V_VAL1'=> 'valOut1'];
    protected $seq = ['V_VAL','V_VAL1'];

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
        $this->assertEquals('TESTPCKG', $this->pckgModel->getPackage());
    }

    public function test_Procedure()
    {
        $this->pckgModel->setProcedure('procedure');
        $this->assertEquals('procedure', $this->pckgModel->getProcedure());
    }

    public function test_GetParameters()
    {
        $this->pckgModel->setDeclarations($this->defaults);
        $this->pckgModel->setSequence($this->seq);
        $this->assertEqualsCanonicalizing( [
            "V_VAL" => ":V_VAL",
            "V_VAL1" => ":V_VAL1"
            ], $this->pckgModel->getParameterList()->toArray());

    }

    public function test_GetDeclarations()
    {
        $this->pckgModel->setDeclarations($this->defaults);
        $this->pckgModel->setSequence($this->seq);
        $this->assertEqualsCanonicalizing( ['V_VAL valOut;','V_VAL1 valOut1;']
            ,$this->pckgModel->getDeclarations()->toArray());

    }

    public function test_ProcedureStatementWithoutDeclarations()
    {
        $this->assertEquals("BEGIN schemastring.TESTPCKG.test_proc();END;",
            $this->pckgModel->getStatementString('test_proc'));
    }

    public function test_ProcedureStatementWithDeclarations()
    {
        $this->pckgModel->setDeclarations($this->defaults);
        $this->pckgModel->setSequence($this->seq);
        $this->assertEquals("DECLARE V_VAL valOut;V_VAL1 valOut1;BEGIN schemastring.TESTPCKG.test_proc(:V_VAL,:V_VAL1);END;",
            $this->pckgModel->getStatementString('test_proc'));
    }

    public function test_ProcedureStatementWithSequence()
    {
        $this->pckgModel->setSequence($this->seq);
        $this->assertEquals("BEGIN schemastring.TESTPCKG.test_proc(:V_VAL,:V_VAL1);END;",
            $this->pckgModel->getStatementString('test_proc'));
    }

}
