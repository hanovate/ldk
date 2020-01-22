<?php

namespace Tests\Unit;

use Unmit\BusinessObjects\Student\Banner\DualCredit\InstitutionAgreement;
use Unmit\BusinessObjects\BusinessObjectItem;
use Tests\TestCase;

/**
 * Class: AbstractBusinessObjectItem
 *
 * Uses Unmit\BusinessObjects\Student\Banner\DualCredit\InstitutionAgreement
 * for testing this class
 *
 * @see TestCase
 */
class AbstractBusinessObjectItem extends TestCase
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

    /**
     * getColumns, getBusinessNames, getByColumn(), getByBusinessName(), toArray, toJson, getArray(), pushElements()
     *
     *
     */
    public function test_getFields()
    {
        $target_elems = [
            new BusinessObjectItem([
                BusinessObjectItem::NAME => 'high school code',
                BusinessObjectItem::COLUMN_NAME => 'szvdist_sbgi_code',
                BusinessObjectItem::BUSINESS_NAME => 'hs-code'
            ]),
            new BusinessObjectItem([
                BusinessObjectItem::NAME => 'district code',
                BusinessObjectItem::COLUMN_NAME => 'szvdist_dist_code',
                BusinessObjectItem::BUSINESS_NAME => 'district-code'
            ]),
            new BusinessObjectItem([
                BusinessObjectItem::NAME => 'district name',
                BusinessObjectItem::COLUMN_NAME => 'szvdist_dist_desc',
                BusinessObjectItem::BUSINESS_NAME => 'district-name'
            ]),
            new BusinessObjectItem([
                BusinessObjectItem::NAME => 'public or not',
                BusinessObjectItem::COLUMN_NAME => 'szvdist_type_ind',
                BusinessObjectItem::BUSINESS_NAME => 'public-or-not'
            ])
        ];

        $target_collection = collect($target_elems);

        $this->assertEquals($this->obj->getFields(),$target_collection);
    }

    public function test_getColumnNames()
    {
        $target_elems = [
            "szvdist_sbgi_code",
            "szvdist_dist_code",
            "szvdist_dist_desc",
            "szvdist_type_ind"
        ];

        $this->assertEquals($this->obj->getColumnNames(),$target_elems);
    }

    public function test_getBusinessNames()
    {
        $target_elems = [
            "hs-code",
            "district-code",
            "district-name",
            "public-or-not"
        ];

        $this->assertEquals($this->obj->getBusinessNames(),$target_elems);
    }

    public function test_getByColumn()
    {
        $target_obj = new BusinessObjectItem([
            BusinessObjectItem::NAME => 'high school code',
            BusinessObjectItem::COLUMN_NAME => 'szvdist_sbgi_code',
            BusinessObjectItem::BUSINESS_NAME => 'hs-code'
        ]);

        $this->assertEquals($this->obj->getByColumnName('szvdist_sbgi_code'),$target_obj);
    }

    public function test_getByBusinessName()
    {
        $target_obj = new BusinessObjectItem([
            BusinessObjectItem::NAME => 'high school code',
            BusinessObjectItem::COLUMN_NAME => 'szvdist_sbgi_code',
            BusinessObjectItem::BUSINESS_NAME => 'hs-code'
        ]);

        $this->assertEquals($this->obj->getByBusinessName('hs-code'),$target_obj);
    }
    public function test_getByName()
    {
        $target_obj = new BusinessObjectItem([
            BusinessObjectItem::NAME => 'high school code',
            BusinessObjectItem::COLUMN_NAME => 'szvdist_sbgi_code',
            BusinessObjectItem::BUSINESS_NAME => 'hs-code'
        ]);

        $this->assertEquals($this->obj->getByName('high school code'),$target_obj);
    }

    public function test_toArray()
    {
        $target_elems = [
            [
                'column-name' => 'szvdist_sbgi_code',
                'name' => 'high school code',
                'business-name' => 'hs-code'
            ],[
                'column-name' => 'szvdist_dist_code',
                'name' => 'district code',
                'business-name' => 'district-code'
            ],[
                'column-name' => 'szvdist_dist_desc',
                'name' => 'district name',
                'business-name' => 'district-name'
            ],[
                'column-name' => 'szvdist_type_ind',
                'name' => 'public or not',
                'business-name' => 'public-or-not'
            ]
        ];

        $this->assertEquals($this->obj->toArray(),$target_elems);
    }

    public function test_toJson()
    {
        $target_json = '{"0":{"business-name":"hs-code","column-name":"szvdist_sbgi_code","name":"high school code"},"1":{"business-name":"district-code","column-name":"szvdist_dist_code","name":"district code"},"2":{"business-name":"district-name","column-name":"szvdist_dist_desc","name":"district name"},"3":{"business-name":"public-or-not","column-name":"szvdist_type_ind","name":"public or not"}}';

        $this->assertTrue($this->obj->toJson() == $target_json);
    }
}
