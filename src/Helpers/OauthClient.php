<?php

namespace Unmit\ldk\Helpers;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use InvalidArgumentException;

/**
 * Class: OauthClient
 *
 * @version 0.1.1 2019-09-23 MH
 * @since 0.1.0
 *
 * @see RESTable
 * @abstract
 */
class OauthClient
{
    const APITOKEN = 'apitoken';
    const TOKEN_TYPE ='token_type';
    const ACCESS_TOKEN ='access_token';
    const EXPIRES_AT = 'expires_at';

    const GET = 'GET';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const PUT = 'PUT';
    const POST = 'POST';
    const CONFIG_PATH = 'app-extra';

    /**
     * @var
     */
    private $hostname;

    protected $httpClient;
    /**
     *  A Request object that is a PSR-7 stream objects, to represent request and response message bodies
     *  HTTP messages consist of a start-line, headers, and a body.
     * @var
     */

    private $httpRequest;

    const CONTENT_TYPE = 'Content-Type';

    /**
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param mixed $hostname
     */
    public function setHostname($hostname)
    {
        if(is_null($hostname))
            throw new \http\Exception\InvalidArgumentException(__CLASS__.__METHOD__.": Hostname cannot be null");
        // set URI-related attributes in a logical order
        $this->setHostname($hostname);

        // instantiate $this->httpClient to GuzzleHttp\Client
        $this->setHttpClient(new Client(['base_uri' => config(OauthClient::CONFIG_PATH . $hostname . '.base-url',null)]));
        if(!$this->sessionCheck())
            throw new ClientException(__CLASS__ . __METHOD__. ": Authorization failed for " . $hostname);
    }
    /**
     * @return Client
     */
    public function getHttpClient()
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
     * @return bool
     */
    public function sessionCheck()
    {
        // pass through if token has been obtained already
        if (session()->has($this->getHostname().'.api.' . self::ACCESS_TOKEN)) {
            return true;
        }

        // pass through if token was previously saved and it hasn't expired
        if (file_exists(storage_path() . '/' . self::APITOKEN)) {
            $saved_apitoken = unserialize(file_get_contents(storage_path() . '/' . self::APITOKEN));
            $current_ut = time();
            if ($current_ut<$saved_apitoken[self::EXPIRES_AT]) {
                session()->push($this->getHostname().'.api.' . self::TOKEN_TYPE, $saved_apitoken[self::TOKEN_TYPE] ?? null);
                session()->push($this->getHostname().'.api.' . self::ACCESS_TOKEN, $saved_apitoken[self::ACCESS_TOKEN] ?? null);
                return true;
            }
        }

        // since a token has not been obtained for this session get one

        return $this->authorize();
    }

    public function authorize()
    {
        // instantiate a Guzzle client
        $client = new \GuzzleHttp\Client();

        // force to use a specific entry in the access database
        // TODO update to use the current login
        $form_params = [
            'client_id' => config(self::CONFIG_PATH . $this->getHostname() . '.client-id'),
            'client_secret' => config(self::CONFIG_PATH . $this->getHostname() . '.client-secret'),
            'grant_type' => 'client_credentials',
            'scope' => '*'
        ];

        // parse the API_BASE_URL for just the host name
        $parsed = parse_url(config(self::CONFIG_PATH. $this->getHostname() . '.base-url'));
        $request_url = $parsed['scheme'].'://'.$parsed['host'] . config(self::CONFIG_PATH . $this->getHostname() . '.auth_uri');

        // accommodate for various development
        $verify = false;
        if (\App::environment(['local','staging'])) {
            if ($certvars = config(self::CONFIG_PATH.'.localcerts',false)) {
                $tmp = array_keys($certvars);
                if (in_array($host = request()->getHttpHost(),$tmp)) {
                    $verify = $certvars[$this->getHostname()];
                }
            }
        }

        // make the request
        $response = $client->request(
            'POST',
            $request_url,
            [
                'form_params' => $form_params,
                'verify'  => $verify
            ]
        );

        // get the response and store the returned values into session variables
        $response->getBody();
        $response_array = json_decode($response->getBody(),true);

        // don't confuse expires_in with the actual expiration time
        $response_array[self::EXPIRES_AT] = time() + $response_array['expires_in'];

        // store
        session()->push($this->getHostname().'.api.' . self::TOKEN_TYPE, $response_array[self::TOKEN_TYPE] ?? null);
        session()->push($this->getHostname().'.api.' . self::ACCESS_TOKEN, $response_array[self::ACCESS_TOKEN] ?? null);

        // save the newly obtained token in a file

        file_put_contents(storage_path() . '/' . self::APITOKEN, serialize($response_array));

        return true;
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

        $url = $this->getApiUrl()."/".$id;

        $this->httpRequest = new \GuzzleHttp\Psr7\Request('GET',$url);
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

        $promise->wait();

        return $returnvalue;
    }

    /**
     * Get a response using an id, if an id is not provided the result should be many.
     *
     * @param  $id
     * @return
     */
    public function get($url) : \Illuminate\Support\Collection
    {
        $response = null;
        $response = $this->httpClient->get($url);
        return $this->responseToCollection($response);
    }

    /**
     * @return bool|int
     * @throws Exception
     */
    public function post($url)
    {
        $response = null;
        try {
            $response = $this->httpClient->post(
                $url, [
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
    public function put($url,$id)
    {
        $response = null;
        if(isset($id)  && count($this->getAttributes()) > 0) {
            $response = $this->httpClient->put(
                $url. "/" . $id, [
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
    public function patch($url,$id)
    {
        $response = null;
        if(isset($id)  && count($this->getAttributes()) > 0) {
            $response = $this->httpClient->patch(
                $url. "/" . $id, [
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
    public function delete($url,$id)
    {
        $response = null;
        if(isset($id)) {
            $response = $this->httpClient->delete($url . "/" . $id);
        }
        return $response->getStatusCode();
    }

    /**
     * Get a response using an array of key => value pairs that will be the basis for query
     *
     * @param  array $compound
     * @return OauthClient|Collection
     */
    public function getWhere($url,array $where)
    {
        $response = $this->httpClient->get($url, ['query' => $where]);
        return $this->responseToCollection($response);
    }

}
