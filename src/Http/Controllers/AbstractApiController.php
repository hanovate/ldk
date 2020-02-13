<?php

namespace Unmit\ldk\Http\Controllers;

use App\Http\Controllers\Controller;
use Unmit\ldk\BusinessObjectInterface;
use Unmit\ldk\Http\Payload;
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
     * @author Ron V Estrada <rvestra@unm.edu>
     *
     * @version 0.1.1 2019-10-11 add field name translation via select()
     *   0.1.0 2019-09-01 phase 1 - a step towards better REST API & OAS
     * @since 0.1.0
     *
     * @todo add more parameters
     */
    public function index(Request $request)
    {

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

            $searchQuery = $this->getResourceModel()->search($searchstr)->
                select($this->getResourceModel()->getBusinessObject()->getSqlSelectItems());
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
     * @author Ron V Estrada <rvestra@unm.edu>
     *
     * @version 0.1.0 2019-08-29 phase 1 - a step towards better REST API & OAS
     * @since 0.1.0
     */
    public function find(Request $request,$id)
    {
        try
        {
            // get an object
            $this->setData($this->getResourceModel()->find($id)->translateToName());
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
     * @author Ron V Estrada <rvestra@unm.edu>
     *
     * @version 0.1.1 2019-10-21 MH
     * @since 0.1.1
     */
    public function store(Request $request)
    {
        // status code; default: 201 Created
        $status_code = 201;

        $response_content = [];

        // save to database
        $success = $this->getResourceModel()->translateToColumn($request->all())->save();

        // get request content as a response if successful
        if ($success) {
            return response()->json($this->translateToName(),$status_code)->header('Access-Control-Allow-Origin','*');
        } else {
            return response()->json(['error'=>__('BAD REQUEST')],500)->header('Access-Control-Allow-Origin','*');
        }


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
     * @author Ron V Estrada <rvestra@unm.edu>
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
        $response_content = null;

        switch ($request->getMethod()) {
            case EnterpriseBaseModel::PUT:
                // find the resource
                $model = $this->getResourceModel()->findOrFail($id);
                // save the resource that came through the request
                $response_content = $model->translateToColumn($request->all())->save();
            case EnterpriseBaseModel::PATCH:
                // save the resource that came through the request
                $values = $request->all();
                $values[BusinessObjectInterface::ID_NAME] = $id;
                $response_content = $this->getResourceModel()->translateToColumn($values)->save();
        }
        return response()->json($response_content->translateToName(),$status_code)->header('Access-Control-Allow-Origin','*');
    }


    /**
     * destroy()
     * Delete an existing resource.
     *
     * @param  Request $request
     * @return Response
     *
     * @author Michael Han <mhan1@unm.edu>
     * @author Ron V Estrada <rvestra@unm.edu>
     *
     * @version 0.1.0
     * @since 0.1.3
     */
    public function destroy($id)
    {
        // reset response variable
        $response_content = [];

        // execute delete operation
        $values[BusinessObjectInterface::ID_NAME] = $id;
        $response_content = $this->getResourceModel()->translateToColumn($values)->delete();

        if ($response_content) {
            // delete operation was a success
            return response()->json($response_content->translateToName(),204)->header('Access-Control-Allow-Origin','*');
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

}
