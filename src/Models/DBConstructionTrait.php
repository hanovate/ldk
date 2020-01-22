<?php

namespace Unmit\ldk\Models;

use http\Exception\InvalidArgumentException;
use Unmit\ldk\BusinessObjectInterface;

/**
 * Trait: DBConstructionTrait
 *
 * @version 0.1.0
 * @since 0.1.0
 */
trait DBConstructionTrait
{

    /**
     * @var string Database schema to locate UNM Oracle Database Objects
     */
    public $schema = null;

    /**
     * @var bool A attribute to indicate that the Pdo object needs a schema
     */
    private $isSchemaDefined = false;

    /** @var BusinessObjectInterface Added to prevent
     * addition of this as a fillable */
    private $businessObject;

    /**
     * @return mixed
     */
    public function getBusinessObject()
    {
        return $this->businessObject;
    }

    /**
     * @return bool
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param bool $schema
     */
    public function setSchema($schema)
    {
        if(empty($schema))
        {
            throw new InvalidArgumentException("Database schema is empty");
        }
        $this->isSchemaDefined = true;
        $this->schema = $schema;
    }

    /**
     * @return bool
     */
    public function isSchemaDefined()
    {
        return $this->isSchemaDefined;
    }

}
