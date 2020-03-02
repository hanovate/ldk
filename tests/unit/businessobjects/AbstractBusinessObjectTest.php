<?php

namespace Unmit\ldk\tests\unit\businessobjects;

use Unmit\ldk\tests\unit\TestBO;
use Unmit\ldk\tests\TestCase;
use Unmit\ldk\BusinessObjects\BusinessObjectItem;

/**
 * Class: AbstractBusinessObjectItem
 *
 * @see TestCase
 */
class AbstractBusinessObjectTest extends TestCase
{
    protected $obj;

    function __construct()
    {
        $this->obj = new TestBO();
        parent::__construct();
    }

    public function testPrerequisites()
    {
        $this->assertTrue(class_exists('Unmit\ldk\tests\unit\TestBO'));
    }

    /**
     * getColumns, getBusinessNames, getByColumn(), getByBusinessName(), toArray, toJson, getArray(), pushElements()
     *
     *
     */
    public function test_getFields()
    {
        $target_elems = [
            new BusinessObjectItem([
                BusinessObjectItem::NAME => 'id',
                BusinessObjectItem::COLUMN_NAME => 'UNMTBL_SEQUENCE_NUM',
                BusinessObjectItem::BUSINESS_NAME => 'Id'
            ]),
            new BusinessObjectItem([
                BusinessObjectItem::NAME => 'title',
                BusinessObjectItem::COLUMN_NAME => 'UNMTBL_TITLE',
                BusinessObjectItem::BUSINESS_NAME => 'Title'
            ])
        ];

        $target_collection = collect($target_elems);

        $this->assertEquals($this->obj->getFields(),$target_collection);
    }

    public function test_getColumnNames()
    {
        $target_elems = [
            "UNMTBL_SEQUENCE_NUM",
            "UNMTBL_TITLE",
        ];

        $this->assertEquals($this->obj->getColumnNames(),$target_elems);
    }

    public function test_getBusinessNames()
    {
        $target_elems = [
            "Id",
            "Title"
        ];

        $this->assertEquals($this->obj->getBusinessNames(),$target_elems);
    }

    public function test_getByColumn()
    {
        $target_obj = new BusinessObjectItem([
            BusinessObjectItem::NAME => 'title',
            BusinessObjectItem::BUSINESS_NAME => 'Title',
            BusinessObjectItem::COLUMN_NAME => 'UNMTBL_TITLE'
        ]);

        $this->assertEquals($this->obj->getByColumnName('UNMTBL_TITLE'),$target_obj);
    }

    public function test_getByBusinessName()
    {
        $target_obj = new BusinessObjectItem([
            BusinessObjectItem::NAME => 'title',
            BusinessObjectItem::BUSINESS_NAME => 'Title',
            BusinessObjectItem::COLUMN_NAME => 'UNMTBL_TITLE'
        ]);

        $this->assertEquals($this->obj->getByBusinessName('Title'),$target_obj);
    }
    public function test_getByName()
    {
        $target_obj = new BusinessObjectItem([
            BusinessObjectItem::NAME => 'title',
            BusinessObjectItem::BUSINESS_NAME => 'Title',
            BusinessObjectItem::COLUMN_NAME => 'UNMTBL_TITLE'
        ]);

        $this->assertEquals($this->obj->getByName('title'),$target_obj);
    }

    public function test_getSqlSelectItems()
    {
        $target_elems = [
            'UNMTBL_SEQUENCE_NUM as id',
            'UNMTBL_TITLE as title'
        ];
        $this->assertEquals($this->obj->getSqlSelectItems(),$target_elems);
    }
    public function test_getNameToColumnNameArray()
    {
        $target_elems = [
            'id'=>'UNMTBL_SEQUENCE_NUM',
            'title' =>'UNMTBL_TITLE'
        ];
        $this->assertEquals($this->obj->getNameToColumnNameArray(),$target_elems);
    }
    public function test_getNameToBusinessNameArray()
    {
        $target_elems = [
            'id'=>'Id',
            'title' =>'Title'
        ];

        $this->assertEquals($this->obj->getNameToBusinessNameArray(),$target_elems);
    }

    public function test_toArray()
    {
        $target_elems = [
            [
                'column-name' => 'UNMTBL_SEQUENCE_NUM',
                'name' => 'id',
                'business-name' => 'Id'
            ],
            [
                'column-name' => 'UNMTBL_TITLE',
                'name' => 'title',
                'business-name' => 'Title'
            ]

        ];

        $this->assertEquals($this->obj->toArray(),$target_elems);
    }

    public function test_toJson()
    {
        $target_json = '{"0":{"business-name":"Id","column-name":"UNMTBL_SEQUENCE_NUM","name":"id"},"1":{"business-name":"Title","column-name":"UNMTBL_TITLE","name":"title"}}';
        $this->assertTrue($this->obj->toJson() == $target_json);
    }
}
