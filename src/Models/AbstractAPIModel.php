<?php

namespace Unmit\ldk\Models;

use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Concerns\GuardsAttributes;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Unmit\ldk\BusinessObjects\BusinessObjectInterface;
use Unmit\ldk\BusinessObjects\BusinessObjectItem;
use Unmit\ldk\Http\Middleware\APIOAuthHandler;
use Unmit\ldk\Traits\HasComponentsTrait;
use Unmit\ldk\Traits\HttpClientUtilsTrait;

/**
 * Class: AbstractAPIModel
 *
 * @version 0.1.1 2019-09-23 MH
 * @since 0.1.0
 *
 * @see RESTable
 * @abstract
 */
abstract class AbstractAPIModel implements RESTable
{
    use HasComponentsTrait,
        HttpClientUtilsTrait,
        HasAttributes,
        HasRelationships,
        HasTimestamps,
        GuardsAttributes;

    const GET = 'GET';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const PUT = 'PUT';
    const POST = 'POST';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $incrementing = false;

    /**
     * @var string business object URI for API target
     *      i.e. /v1/hr/person/,/v3/hr/employee, student, etc.; e.g. '/v1/student/dual-credit'
     */
    private $apiEntityUri;

    /**
     * base url + entity uri
     * @var string Fully qualified URI for API target
     */
    private $apiUri;

    /**
     * @var string The key structure that is the api pivot field
     *  (i.e. default id, Student, Section, etc.)
     */
    private $apiKeyField = 'id';

    /**  Guzzle Client - Initialized when constructed */

    /**
     *  a PHP HTTP client that makes it easy to send HTTP requests
     * Abstracts away the underlying HTTP transport
     * @var
     */

    protected $httpClient;
    /**
     *  A Request object that is a PSR-7 stream objects, to represent request and response message bodies
     *  HTTP messages consist of a start-line, headers, and a body.
     * @var
     */

    private $httpRequest;

    /**
     * Base Url of the client that is merged into relative URIs.
     * When a relative Url is provided to a client, the client will combine the base URI with the relative URI
     * @var
     */

    private $baseUrl;
    /**
     * @var
     */
    protected $businessObject;

    const BEARER = 'Bearer';
    const CONTENT_TYPE = 'Content-Type';
    const AUTHORIZATION = 'Authorization';

    /**
     * __construct() for AbstractAPIModel
     *
     * @param BusinessObjectInterface $businessObject
     * @param string $apiEntityUri
     *
     * @version 0.1.0
     * @since 0.1.0
     */
    public function __construct(
        BusinessObjectInterface $businessObject,
        $apiEntityUri)
    {
        // set URI-related attributes in a logical order
        $this->setApiEntityUri($apiEntityUri);

        // set $this->businessObject
        $this->setBusinessObject($businessObject);

        // populate $this->fillable
        $this->fillable = $businessObject->getNames();

        // instantiate $this->httpClient to GuzzleHttp\Client
        $this->setHttpClient(new Client(['base_uri' => config(APIOAuthHandler::API_CONFIG_PATH . '.base-url',null)]));
    }

    public function getIncrementing()
    {
        return $this->incrementing;
    }

    /**
     * @return mixed
     */
    public function getBusinessObject()
    {
        return $this->businessObject;
    }

    /**
     * @param mixed $businessObject
     */
    public function setBusinessObject(BusinessObjectInterface $businessObject)
    {
        $this->businessObject = $businessObject;
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @param Client $httpClient
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return mixed
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @param mixed $httpRequest
     */
    public function setHttpRequest(Request $httpRequest): void
    {
        $this->httpRequest = $httpRequest;
    }
    /**
     * @return array
     */
    public function getHeaders(): array
    {
        $token = null;
        if (session()->has('api.' . APIOAuthHandler::ACCESS_TOKEN)) {
            $session = session()->get('api.' . APIOAuthHandler::ACCESS_TOKEN);
            $token = array_pop($session);
        }
        else{
            throw OAuthServerException::invalidRequest('Access token is invalid, expired, or unusable');
        }
        //$this->refreshAccessToken();
        return [self::AUTHORIZATION => self::BEARER.' '.$token, self::CONTENT_TYPE => 'application/json'];
    }
    /**
     * @return string
     */
    public function getApiKeyField(): string
    {
        if($this->usePrefix) {
            //The underscore is due to the banner api pattern of table_id
            return $this->getApiEntityUri().'_'.$this->apiKeyField;
        }
        return $this->apiKeyField;
    }

    /**
     * @param string $apiKeyField
     */
    public function setApiKeyField(string $apiKeyField)
    {
        $this->apiKeyField = $apiKeyField;
    }

    /**
     * Set all URI related attributes
     *
     * @param string $apiEntityUri
     *
     * @version 0.1.0 2019-09-23 MH
     * @since 0.1.0
     */
    public function setApiUri($apiEntityUri)
    {
        // if base URI is not set, then abort
        if (is_null($this->baseUrl)) {
            abort(404,'API base URL (config: ' . APIOAuthHandler::API_CONFIG_PATH . '.base_url) is not set');
        }

        // set API entity URI
        $this->setApiEntityUri($apiEntityUri);

        // set fully qualified API URI
        $this->setApiUri(trim($this->baseUrl,'/')
            .'/'.trim($this->getApiEntityUri(),'/'));
    }

    /**
     * Set $this->ApiEntityUri
     *
     * @param string $apiEntityUri
     *
     * @todo there should be a singularly responsible class for managing
     *       the Api versioning + uri
     *
     * @version 0.1.2 2019-09-23 MH
     * @since 0.1.0
     */
    public function setApiEntityUri($apiEntityUri)
    {
        // set apiEntityUri
        $this->apiEntityUri = $apiEntityUri;
    }

    /**
     * Get $this->ApiEntityUri
     *
     * @return string
     *
     * @version 0.1.0 2019-09-21 MH
     * @since 0.1.0
     */
    public function getApiEntityUri()
    {
        return $this->apiEntityUri;
    }

    /**
     * Get the fully qualified API URI.
     *
     * @return string A fully qualified URI to use for API access.
     *
     * @version 0.1.0 2019-09-21 MH
     * @since 0.1.0
     */
    public function getApiUri()
    {
        return $this->apiUri;
    }


    /**
     * Get a response using an id, if an id is not provided the result should be many.
     *
     * @param  $id
     * @return mixed
     *
     * @version 0.1.1 2019-09-23 MH
     * @since 0.1.0
     *
     * @todo return a mapped string to HTTP status code
     */
    public function find($id = null)
    {
        $response = $returnvalue = null;

        // throw an exception if $id is not set
        if (!isset($id)) {
            throw new InvalidArgumentException("id cannot be null when getting a model");
        }

        $uri = $this->getApiUri()."/".$id;

        $this->httpRequest = new \GuzzleHttp\Psr7\Request('GET',$uri);
        $promise = $this->getHttpClient()
            ->sendAsync($this->httpRequest,[
                'timeout' => config('api.request_timeout',10),
                'verify'  => (((\App::environment(['local','staging'])) && (request()->getHttpHost()=='core.unm.edu')) ? (resource_path().'/data/rootCA.pem'):'') // DEBUG
            ])
            ->then(function ($response) use (&$returnvalue) {

                if ($response->getStatusCode()==200) {
                    $returnvalue = $this->responseToModel($response, BusinessObjectItem::NAME);
                } else {
                    $returnvalue = $response->json(
                        ['error' => 'Error occurred'], // TODO: return a mapped string to HTTP status code
                        $response->getStatusCode()
                    );
                }
            });
//        $promise->then(
//            function (ResponseInterface $res) {
//                echo $res->getStatusCode() . "\n";
//            },
//            function (RequestException $e) {
//                echo $e->getMessage() . "\n";
//                echo $e->getRequest()->getMethod();
//            }
//        );

        $promise->wait();

        return $returnvalue;
    }

    /**
     * Get a response using an id, if an id is not provided the result should be many.
     *
     * @param  $id
     * @return
     */
    public function lookup(array $keys)
    {
        $response = null;
        $id = implode('/',$keys);
        if(isset($id)) {
            $response = $this->httpClient->get($this->getApiUri()."/lookup/".$id);
            return $this->responseToModel($response);
        }
        else{
            throw new InvalidArgumentException("id cannot be null when getting a model");
        }
    }
    /**
     * Get a response using an id, if an id is not provided the result should be many.
     *
     * @param  $id
     * @return
     */
    public function get() : \Illuminate\Support\Collection
    {
        $response = null;
        $response = $this->httpClient->get($this->getApiUri());
        return $this->responseToCollection($response);
    }

    /**
     * @return bool|int
     * @throws Exception
     */
    public function post()
    {
        $response = null;
        try {
            $response = $this->httpClient->post(
                $this->getApiUri(), [
                    'form_params' => $this->getAttributes()
                ]
            );
        }catch (ServerException $se){
            throw new Exception("Post Failed: ".$se->getMessage());
        }catch (Exception $e) {
            throw new Exception("Post Failed: ".$e->getMessage());
        }
        switch ($response->getStatusCode()){
            case '201':
                return $this->responseToModel($response);
            default:
                throw new Exception("Post Failed: ".$response->getStatusCode());
        }
    }


    /**
     * @param  $id
     * @return bool|int
     * @throws Exception
     */
    public function put($id)
    {
        $response = null;
        if(isset($id)  && count($this->getAttributes()) > 0) {
            $response = $this->httpClient->put(
                $this->getApiUri(). "/" . $id, [
                    'form_params' => $this->getAttributes()
                ]
            );
        }
        switch ($response->getStatusCode()){
            case '200':
                return true;
            default:
                throw new Exception("Put Failed: ".$response->getStatusCode());
        }
    }

    /**
     * @param  $id
     * @return bool|int
     * @throws Exception
     */
    public function patch($id)
    {
        $response = null;
        if(isset($id)  && count($this->getAttributes()) > 0) {
            $response = $this->httpClient->patch(
                $this->getApiUri(). "/" . $id, [
                    'form_params' => $this->getAttributes()
                ]
            );
        }
        switch ($response->getStatusCode()){
            case '200':
                return true;
            default:
                throw new Exception("Patch Failed: ".$response->getStatusCode());
        }
    }


    /**
     * @param  $id
     * @return bool|int
     * @throws Exception
     */
    public function delete($id)
    {
        $response = null;
        if(isset($id)) {
            $response = $this->httpClient->delete($this->getApiUri() . "/" . $id);
        }
        return $response->getStatusCode();
    }

    /**
     * Get a response using an array of key => value pairs that will be the basis for query
     *
     * @param  array $compound
     * @return AbstractAPIModel|Collection
     */
    public function getWhere(array $where)
    {
        $response = $this->httpClient->get($this->getApiUri(), ['query' => $where]);
        return $this->responseToCollection($response);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        foreach ($this->fillableFromArray($attributes) as $key => $value) {

            $this->setAttribute($key, $value);
        }
        $this->syncOriginal();
        return $this;
    }

    /**
     *
     * to an array with business-name as keys for only fillable attributes
     *
     * @param array $attr
     *
     * @return array
     *
     * @version 0.1.0 2019-09-24 MH initial write-up
     * @since 0.1.2 2019-09-24
     */
    protected function getArrayWithBusinessKeys($attr)
    {
        // initialize the array to use as a return value
        $return_array = [];

        // get the array from the BO as a reference
        // to find the corresponding keys
        $bo_array = $this->getBusinessObject()->toArray();

        // loop through the $attr to convert each element
        // data elements only found in the BO are converted
        foreach ($attr as $k=>$v) {
            if (!(($key = array_search($k,array_column($bo_array,
                    BusinessObjectItem::COLUMN_NAME))) === false)) {

                // $key is found, so use it get the business-name
                // from the reference array and assign the current value ($v)
                $return_array[$bo_array[$key]['business-name']] = $v;

            }
        }

        // return the constructed array
        return $return_array;
    }

    /**
     *
     * to an array with name as keys for only fillable attributes
     *
     * @param array $attr
     *
     * @return array
     *
     * @version 0.1.0 2019-11-16 RVE
     * @since 0.1.2 2019-11-16
     */
    protected function getArrayWithNames($attr)
    {
        // initialize the array to use as a return value
        $return_array = [];

        // get the array from the BO as a reference
        // to find the corresponding keys
        $bo_array = $this->getBusinessObject()->toArray();

        // loop through the $attr to convert each element
        // data elements only found in the BO are converted
        foreach ($attr as $k=>$v) {
            if (!(($key = array_search($k,array_column($bo_array,
                    BusinessObjectItem::COLUMN_NAME))) === false)) {

                // $key is found, so use it get the business-name
                // from the reference array and assign the current value ($v)
                $return_array[$bo_array[$key][BusinessObjectItem::NAME]] = $v;

            }
        }

        // return the constructed array
        return $return_array;
    }

    /**
     * Pass in a GuzzleHttp\Client's Response and fill Model(s)
     *
     * @param array $response
     *
     * @return AbstractAPIModel|Collection
     *
     * @version 0.1.2 2019-09-24 MH
     * @since 0.1.0
     */
    protected function responseToModel($response,$withType)
    {
        $results = $this->getArrayFromClientResponse($response);

        if (isset($results[0])) {
            if (count($results) > 1) {
                throw new MultiplesFoundException();
            } else {
                switch ($withType){
                    case BusinessObjectItem::BUSINESS_NAME:
                        return $this->fill($this->getArrayWithBusinessKeys($results->first()));
                    case BusinessObjectItem::NAME:
                        return $this->fill($this->getArrayWithNames($results->first()));
                }
            }
        } elseif (is_array($results)) {
            switch ($withType){
                case BusinessObjectItem::BUSINESS_NAME:
                    return $this->fill($this->getArrayWithBusinessKeys($results));
                case BusinessObjectItem::NAME:
                    return $this->fill($this->getArrayWithNames($results));
            }
        } else {
            throw new ModelNotFoundException();
        }
    }

    /**
     * Pass in a Http Client Response and fill Model(s)
     *
     * @param  array $response
     * @return AbstractAPIModel|Collection
     */
    protected function responseToCollection($response, $withType)
    {
        $results = $this->getArrayFromClientResponse($response);

        $collection = new Collection();

        if (empty($results)) {
            throw new ModelNotFoundException('Collection cannot add Model, query is empty');
        }
        if($results['total'] > 0)
        {
            if (count($results) > 0) {
                if (isset($results[0])) {
                    foreach ($results as $row) {
                        $component = new static();
                        switch ($withType) {
                            case BusinessObjectItem::BUSINESS_NAME:
                                $collection->push($component->fill($this->getArrayWithBusinessKeys($results)));
                            case BusinessObjectItem::NAME:
                                $collection->push($component->fill($this->getArrayWithNames($results)));
                        }
                    }
                } else {
                    $component = new static();
                    switch ($withType) {
                        case BusinessObjectItem::BUSINESS_NAME:
                            $collection->push($component->fill($this->getArrayWithBusinessKeys($results)));
                        case BusinessObjectItem::NAME:
                            $collection->push($component->fill($this->getArrayWithNames($results)));
                    }
                }
            }
        }
        return $collection;
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }
}
