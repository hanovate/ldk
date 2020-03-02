<?php

namespace Unmit\ldk\tests\unit;

use Illuminate\Support\Collection;
use Unmit\ldk\BusinessObjects\AbstractBusinessObject;
use Unmit\ldk\BusinessObjects\BusinessObjectItem;
/**
 * Class: TestBO
 *
 * @see AbstractBusinessObject
 *
 * @version 0.1.0 RVE
 * @since 0.1.0
 */
class TestBO extends AbstractBusinessObject
{
    public $id;
    public $title;


    public function __construct()
    {
        $items = new Collection();

        $elems = [
            [
                BusinessObjectItem::NAME => 'id',
                BusinessObjectItem::BUSINESS_NAME => 'Id',
                BusinessObjectItem::COLUMN_NAME => 'UNMTBL_SEQUENCE_NUM'
            ],[
                BusinessObjectItem::NAME => 'title',
                BusinessObjectItem::BUSINESS_NAME => 'Title',
                BusinessObjectItem::COLUMN_NAME => 'UNMTBL_TITLE'
            ],
        ];

        $this->pushElements($items,$elems);

        $this->setFields($items);
    }

}