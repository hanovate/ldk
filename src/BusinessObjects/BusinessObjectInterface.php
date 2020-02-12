<?php

namespace Unmit\ldk\BusinessObjects;

use Illuminate\Support\Collection;

/**
 * Interface: BusinessObjectInterface
 *
 */
interface BusinessObjectInterface
{
    const ID_NAME = 'id';
    /**
     * @param Collection $fields
     *
     * @todo type checking: void
     */
    public function setFields(Collection $fields);

    /**
     * @return Collection
     */
    public function getFields();

    /**
     * @return mixed
     */
    public function getIdColumnName();

    /**
     * @return array
     *
     * @todo type checking: array
     */
    public function getColumnNames();

    /**
     * @return array
     *
     * @todo type checking: array
     */
    public function getBusinessNames();
    /**
     * @return array
     *
     * @todo type checking: array
     */
    public function getNames();

    /**
     * @param string $column
     * @return BusinessObjectItem
     */
    public function getByColumnName($column);

    /**
     * @param string $businessName
     * @return mixed
     */
    public function getByBusinessName($businessName);

    /**
     * @return mixed
     */
    public function getSqlSelectString();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return json
     */
    public function toJson();

}
