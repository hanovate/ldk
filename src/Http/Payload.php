<?php


namespace Unmit\ldk\Http;


use Illuminate\Database\Eloquent\Collection;

class Payload
{
    const LINKS = 'links';
    const DATA = 'data';
    const TOTAL = 'total';
    const REQUEST_ERRORS = 'errors';
    const ID = 'id';

    private $id;
    private $data;
    private $total;
    private $links = array();
    private $errors;

    /**
     * @return mixed
     */
    public function getId()
    {
        if(is_null($this->id)){
            return Payload::ID;
        }
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        //
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        // get total if it exists
        if (is_a($this->getData(),Collection::class)) {
            $total_count = $this->getData()->first()->total_count ?? null;
        } else {
            $total_count = $this->getData()->total_count ?? null;
        }
        return $total_count;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param mixed $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }
    /**
     * @return mixed
     */
    public function getLink($type)
    {
        return $this->links[$type];
    }

    /**
     * @param mixed $links
     */
    public function setLink($type, $link)
    {
        $this->links[$type] = $link;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $output = array();
        // links.self the full url invoked to get this
        foreach ($this->getLinks() as $type => $link)
        {
            $output[Payload::LINKS][$type] = $link;
        }

        if (empty($this->getErrors())) {
            // data content is found here
            $output[Payload::DATA] = $this->getData();

            // get total if it exists
            if ($this->getTotal())
                $output[Payload::TOTAL] = $this->getTotal();

            // get primary key name
            $output[Payload::ID] = $this->getId();

        } else
        {
            // there's no data, so return an empty array
            $output[Payload::REQUEST_ERRORS] = $this->getErrors();
            $output[Payload::TOTAL] = $this->getTotal();
        }
        return $output;
    }
}