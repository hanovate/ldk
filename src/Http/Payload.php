<?php


namespace Unmit\ldk\Http;


use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseConstants;

class Payload
{
    const LINKS = 'links';
    const DATA = 'data';
    const TOTAL = 'total';
    const REQUEST_ERRORS = 'errors';
    const ID = 'id';
    const COUNT = 'count';
    const RESPONSE_STATUS = 'code';


    private $id;
    private $data;
    private $total;
    private $count;
    private $limit;
    private $offset;
    private $links = array();
    private $errors;
    private $status;


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
    public function setDataArray($array)
    {
        $this->data = $array;
    }

    /**
     * @param $str
     */
    public function setDataString($str)
    {
        $this->data = [Payload::ID => $str];
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        // get total if it data exists
        if (empty($this->getData()))
        {
            return 0;
        }
        return count($this->getData());
    }
    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
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
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit, $offset = null)
    {
        $this->limit = $limit;
        if(isset($offset))
            $this->offset = $offset;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
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
     * @return mixed
     */
    public function getStatusDescription()
    {
        return ResponseConstants::statusText[intval($this->getStatus())];
    }
    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->getStatus();
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
            // @todo: return appropriate status code

        } else
        {
            // there's no data, so return an empty array
            $output[Payload::REQUEST_ERRORS] = $this->getErrors();
            // @todo: translate($exception) return status code
            $output[Payload::COUNT] = $this->getCount();
        }
        $output[Payload::TOTAL] = $this->getTotal();
        return $output;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}