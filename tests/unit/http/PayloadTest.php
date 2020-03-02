<?php

namespace Unmit\ldk\tests\unit\http;

use Unmit\ldk\tests\TestCase;
use Unmit\ldk\Http\Payload;

class PayloadTest extends TestCase
{
    public $payload = null;
    public $data = array(["id" => "1111111",
    "netid" => "TEST",
    "business_reason" => "test before release"]);

    public function __construct()
    {
        parent::__construct();
        $this->payload = new Payload();

    }

    public function testGetLimit()
    {

        $this->payload->setLimit(100, 500);
        $this->assertEquals($this->payload->getLimit(),100);
        $this->assertEquals($this->payload->getOffset(),500);

    }

//    public function testGetErrors()
//    {
//        @todo: test our error message after implementation learning
//    }

    public function test_GetData()
    {
        $this->payload->setDataArray($this->data);
        $this->assertEquals($this->payload->getData(),$this->data);
    }

    public function testToJson()
    {
        $this->payload->setDataArray($this->data);
        $this->payload->setLink('self', "http://rest-api.unm.edu/api/v1/domain/area/action" );
        $this->payload->setTotal(1);

        $jsonStr = '{"links":{"self":"http:\/\/rest-api.unm.edu\/api\/v1\/domain\/area\/action"},"data":[{"id":"1111111","netid":"TEST","business_reason":"test before release"}],"total":1,"id":"id"}';
        $this->assertEquals($this->payload->toJson(),$jsonStr);
    }
}
