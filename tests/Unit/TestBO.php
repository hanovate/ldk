<?php

namespace Unmit\Api\Tests\Unit;

use Illuminate\Support\Collection;
use Unmit\BusinessObjects\AbstractBusinessObject;
use Unmit\BusinessObjects\BusinessObjectItem;
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

        // Sequence Number (Unique Identifier)
        $sequenceId = new BusinessObjectItem('id','Id','ID');
        $items->push($sequenceId);

        // Title
        $title = new BusinessObjectItem('title', 'Title', 'TABLE_TITLE');
        $items->push($title);

        $this->setFields($items);
    }

}