<?php

namespace Unmit\ldk\Models;

use Exception;
use Illuminate\Support\Collection;
use Unmit\ldk\BusinessObjectInterface;


interface RESTable
{
    /**
     * @return mixed
     */
    public function getBusinessObject();

    /**
     * @param mixed $businessObject
     */
    public function setBusinessObject(BusinessObjectInterface $businessObject);

    /**
     * Get a response using an id, if an id is not provided the result should be many.
     *
     * @param  $id
     * @return
     */
    public function find($id = null);

    /**
     * Get a response using an id, if an id is not provided the result should be many.
     *
     * @param  $id
     * @return
     */
    public function get() : Collection;

    /**
     * @return bool|int
     * @throws Exception
     */
    public function post();

    /**
     * @param  $id
     * @return bool|int
     * @throws Exception
     */
    public function put($id);

    /**
     * @param  $id
     * @return bool|int
     * @throws Exception
     */
    public function patch($id);

    /**
     * @param  $id
     * @return bool|int
     * @throws Exception
     */
    public function delete($id);

}
