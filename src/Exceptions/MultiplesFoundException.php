<?php

namespace Unmit\ldk\Exceptions;

use Exception;

class MultiplesFoundException extends Exception
{
    protected $ids;

    /**
     * Set the affected Eloquent model and instance ids.
     *
     * @param  string    $model
     * @param  int|array $ids
     * @return $this
     */
    public function setIds($ids = [])
    {
        $this->ids = Arr::wrap($ids);

        $this->message = "API request resulted in multiple results";

        if (count($this->ids) > 0) {
            $this->message .= ' '.implode(', ', $this->ids);
        } else {
            $this->message .= '.';
        }

        return $this;
    }
}
