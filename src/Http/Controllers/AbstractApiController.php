<?php

namespace Unmit\ldk\Http\Controllers;

use App\Http\Controllers\Controller;
use Unmit\ldk\Http\Payload;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * abstract class AbstractApiController
 *
 * Controller to consolidate the common actions that are related to a Models entity.  This controller
 * is designed for a one to one relation with a Models entity.  If a controller is needing to work with
 * multiple Models objects this class likely not be efficient.  Actions that will need to be implemented in the
 * extending class will be create() and custom queries.
 *
 * @author Ron Estrada <rvestra@unm.edu>
 * @author Michael Han <mhan1@unm.edu>
 *
 * @version 0.1.0
 */

abstract class AbstractApiController extends Controller
{
    /**
     * The Models object that will likely be instantiated during the extended controller construction.
     */
    protected $resourceModel;
    protected $payload;
    private $data;
    private $errors;

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
        $this->data = $data;
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
    public function getResourceModel()
    {
        return $this->resourceModel;
    }

    /**
     * @param mixed $resourceModel
     */
    public function setResourceModel($resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    /**
     * __construct()
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // initialize payload array
        $this->payload = new Payload();
    }



    /**
     * index()
     * Display a list of all of the entities.
     *
     * @param \Request $request
     *
     * @return Response
     *
     * @author Michael Han <mhan1@unm.edu>
     *
     * @version 0.1.1 2019-10-11 add field name translation via select()
     *   0.1.0 2019-09-01 phase 1 - a step towards better REST API & OAS
     * @since 0.1.0
     *
     * @todo add more parameters
     */
    public function index(Request $request)
    {
        // make a string for field name translation from column name
        // to business name to be used in select()
//        $fields = $this->getResourceModel()->getBusinessObject()->getFields();
//        foreach ($fields as $v) {
//            $selectstr[] = $v->getColumnName().' as '.$v->getBusinessName();
//            $selectAltNames[] = $v->getColumnName();
//        }

        // TODO: add more parameters here & below (e.g. sort, orderby, etc)
        // set whereColumn parameter
        // get objects and pass on the parameters
        try
        {
            // get search parameters
            // get query string from the URI
            $qstring = parse_url($request->getRequestUri(),PHP_URL_QUERY);
            parse_str($qstring,$qstr);

            // check for limit parameter
            $limit = $qstr['limit'] ?? null;

            // check for offset parameter
            $offset = $qstr['start'] ?? null;
            $this->payload->setLimit($limit,$offset);

            // check for query string parameter
            $searchstr = $qstr['query'] ?? false;

//            $columns = \DB::raw(implode(',',->getNameToColumnNameArray()));
            $searchQuery = $this->getResourceModel()->search($searchstr)->select($this->getResourceModel()->getBusinessObject()->getSqlSelectItems());
            $this->payload->setTotal($searchQuery->count());
            $this->setData($searchQuery->when($limit,function($query,$limit) {
                return $query->limit($limit); // limit
            })->when($offset,function($query,$offset) {
                return $query->offset($offset);  // offset
            })->get());

            // links.self the full url invoked to get this
            // @todo: need to handle other type of links
            $this->payload->setLink('self',$request->fullUrl());
        }
        catch(\Exception $e)
        {
            $this->setErrors([$e->getCode() => $e->getMessage()]);
        }
        // get payload
        $this->payload = $this->getPayload();

        // TODO: remove ACAO header tag after token auth is added
        return response()->json($this->payload->toArray())->header('Access-Control-Allow-Origin','*');
    }

    /**
     * find()
     * Display a resource.
     *
     * @param \Request $request to grab session info
     * @param mixed $id identifier for the business object
     *
     * @return Response
     *
     * @author Michael Han <mhan1@unm.edu>
     *
     * @version 0.1.0 2019-08-29 phase 1 - a step towards better REST API & OAS
     * @since 0.1.0
     */
    public function find(Request $request,$id)
    {
        try
        {
            // get an object
            $this->setData($this->getResourceModel()->find($id));
        }
        catch(\Exception $e)
        {
            $this->setErrors([$e->getCode() => $e->getMessage()]);
        }

        // get payload
        $payload = $this->getPayload();

        // return payload in JSON format
        return response()->json($payload->toArray())->header('Access-Control-Allow-Origin','*');
    }

    /**
     * store()
     * Process create (POST) request. This method should be implemented
     * by class that extends this one.
     *
     * @param Request $request
     *
     * @todo
     *   1. for already existing data (or via oci8 error handling) 202
     *   2. for empty fields for required fields
     *   3. for other cases
     *
     * @author Michael Han <mhan1@unm.edu>
     *
     * @version 0.1.1 2019-10-21 MH
     * @since 0.1.1
     */
    public function store(Request $request)
    {
        // status code; default: 201 Created
        $status_code = 201;

        $response_content = [];

        // translate business names to column names
        $nameMap = $this->getResourceModel()->getBusinessObject()->getBusinessNameToColumnNameArray();
        $incomingContent = $request->all();
        foreach ($incomingContent as $k => $v) {
            $incomingContent[$nameMap[$k]] = $v;
            unset($incomingContent[$k]);
        }

        // save to database
        $success = $this->getResourceModel()->fill($incomingContent)->save();

        // get request content as a response if successful
        if ($success) {
            $response_content = $incomingContent;
        } else {
            $status_code = 500; // DB save didn't occur properly
        }

        return response()->json($response_content,$status_code)->header('Access-Control-Allow-Origin','*');
    }

    /**
     * update()
     * Edit an existing resource.
     *
     * @param Request $request
     * @param mixed $id
     *
     * @return Response
     *
     * @author Michael Han <mhan1@unm.edu>
     *
     * @version 0.1.1 2019-10-21 MH add biz name to col name conversion
     * @since 0.1.2
     *
     * and differentiation in responses
     */
    public function update(Request $request, $id)
    {
        // status code; default to 200 OK
        $status_code = 200;

        $response_content = [];

        // translate names to column names
        $nameMap = $this->getResourceModel()->getBusinessObject()->getBusinessNameToColumnNameArray();
        $incomingContent = $request->all();
        // todo: move this to method toggleToColunmName(namesArray) : array() on AbstractBusinessObject
        foreach ($incomingContent as $k => $v) {
            if (isset($nameMap[$k])) {
                $incomingContent[$nameMap[$k]] = $v;
            }
            unset($incomingContent[$k]);
        }

        // find the resource
        $this->resourceModel = $this->resourceModel->findOrFail($id);

        // save the resource that came through the request
        $success = $this->getResourceModel()->fill($incomingContent)->save();

        if ($success) {
            $response_content = $incomingContent;
        } else {
            // TODO: differentiate between different type of errors
            $status_code = 500;
        }

        switch ($request->getMethod()) {
            case EnterpriseBaseModel::PUT:
                // todo: Add find() then fill(request->all()) and finally save()
            case EnterpriseBaseModel::PATCH:
                // todo: this only needs fill(request->all()) and save()
                return response()->json($response_content,$status_code)->header('Access-Control-Allow-Origin','*');
        }
    }


    /**
     * destroy()
     * Delete an existing resource.
     *
     * @param  Request $request
     * @return Response
     *
     * @author Michael Han <mhan1@unm.edu>
     *
     * @version 0.1.0
     * @since 0.1.3
     */
    public function destroy($id)
    {
        // reset response variable
        $response_content = [];

        // execute delete operation
        $success = $this->getResourceModel()->find($id)->delete();

        if ($success) {
            // delete operation was a success
            return response()->json($response_content,204)->header('Access-Control-Allow-Origin','*');
        } else {
            // an error was detected on the backend system
            return response()->json(['error'=>__('BAD REQUEST')],400)->header('Access-Control-Allow-Origin','*');
        }
    }
    /**
     * getPayload()
     *
     * @param Request $request
     * @param mixed $obj
     * @param boolean $forceFieldNameTranslation Only if $obj is a record
     *
     * @return array
     *
     * @author Michael Han <mhan1@unm.edu>
     * @author Ron V Estrada <rvestra@unm.edu>
     *
     * @version 0.1.1 2019-10-18 MH
     * @since 0.1.0
     */
    protected function getPayload()
    {
        // get id column name
        $bo = $this->getResourceModel()->getBusinessObject();
        if(isset($bo))
        {
            $this->payload->setId($bo->getIdColumnName());
        }

        $payloadErrors = $this->getErrors();
        if(!empty($payloadErrors)) {
            // there's no data, so return an empty array
            $this->payload->setErrors($payloadErrors);
        }
        else
        {
            // data can be Collection, array, string, Model
            $dataType = gettype($this->getData());
            switch ($dataType) {
                case 'string':
                    // default to id
                    $this->payload->setDataString($this->getData());
                    break;
                case 'array':
                    $this->payload->setDataArray($this->getData());
                    break;
                case 'object':
                    $this->payload->setDataArray($this->getData()->toArray());
                    break;
                default:
                    $this->payload->setErrors(['message' => 'model response data type unmatched']);
                    break;
            }
        }

        return $this->payload;
    }


    /**
     * Take a payload record(s) as key / value and toggle from Column Name -> Name
     * When $payload can be:
     *  Model
     *  array(column, value)
     *  Collection of Models
     *
     * @param $payload
     * @return array
     */
    public function translateOutboundData($payload)
    {
        if ($payload instanceof Model) {
            return $this->translateToName($payload->toArray());
        }
        elseif (is_array($payload))
        {
            return $this->translateToName($payload);
        }
        elseif ($payload instanceof Illuminate\Database\Eloquent\Collection)
        {
            $translation = array();
            $payload->each(function ($value,$key) use (&$translation){
                $item = $this->translateToName($value->toArray());
                return $translation->push($item);
            });

        }
    }

    /**
     * Take a column / value array of and toggle from Column Name -> Name
     *
     * @param array $columns
     * @return array
     */
    public function translateToName(array $columns)
    {
        $businessObject = $this->getResourceModel()->getBusinessObject();
        $translation = array();

        collect($columns)->reject(function ($value, $key) {
            return empty($value);
        })->each(function ($value,$key) use (&$translation, $businessObject){
            $item = $businessObject->getByColumnName(strtoupper($key));
            if(isset($item))
                return $translation[$item->getName()] = $value;
        });
        return $translation;
    }


    /**
     * Take a names / value array of and toggle from Name -> Column Name
     *
     * @param array $names
     * @return array
     */
    public function translateToColumnName(array $names)
    {
        $businessObject = $this->getResourceModel()->getBusinessObject();
        $translation = array();

        collect($names)->reject(function ($value, $key) {
            return empty($value);
        })->each(function ($value,$key) use (&$translation, $businessObject){
            $item = $businessObject->getByName($key);
            if(isset($item))
                return $translation[$item->getColumnName()] = $value;
        });
        return $translation;
    }
}
