<?php

namespace Unmit\ldk\Helpers;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use InvalidArgumentException;
use Laravel\Passport\Exceptions\OAuthServerException;

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

    const CONTENT_TYPE = 'Content-Type';
    const AUTHORIZATION = 'Authorization';

    const GET = 'GET';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const PUT = 'PUT';
    const POST = 'POST';
    const CONFIG_PATH = 'app-extra';
    /**
     * @var
     */
    private $service;

    protected $httpClient;
    /**
     *  A Request object that is a PSR-7 stream objects, to represent request and response message bodies
     *  HTTP messages consist of a start-line, headers, and a body.
     * @var
     */

    private $httpRequest;
    private $tokenStorage;

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        if(is_null($service))
            throw new \http\Exception\InvalidArgumentException(__CLASS__.__METHOD__.": Service cannot be null");
        // set URI-related attributes in a logical order
        $this->service = $service;

        // instantiate $this->httpClient to GuzzleHttp\Client
        $this->setHttpClient(new Client(['base_uri' => config($this->getRestConfigPath() . '.base-url',null)]));
        if(!$this->sessionCheck())
            throw new ClientException(__CLASS__ . __METHOD__. ": Authorization failed for " . $service);
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
        if (session()->has($this->getService().'.api.' . self::ACCESS_TOKEN)) {
            if($this->isFresh())
            {
                return true;
            }
        }

        // since a token has not been obtained for this session get one

        return $this->authorize();
    }
    /**
     * @return bool
     */
    public function getCertificates()
    {
        // accommodate for various development
        $verify = false;
        if (\App::environment(['local','staging'])) {
            if ($certvars = config($this->getRestConfigPath().'.'.$this->getService().'.cert',false)) {
                $tmp = array_keys($certvars);
                if (in_array($host = request()->getHttpHost(),$tmp)) {
                    $verify = $certvars[$host];
                }
            }
        }

        return $verify;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        if (session()->has($this->getService().'.api.' . self::ACCESS_TOKEN)) {
            $session = session()->get($this->getService());
            $token = array_pop($session);
        }
        else{
            throw OAuthServerException::invalidRequest('Access token is invalid, expired, or unusable');
        }
        //$this->refreshAccessToken();
        return [self::AUTHORIZATION => $token[self::TOKEN_TYPE].' '.$token[self::ACCESS_TOKEN], self::CONTENT_TYPE => 'application/json'];
    }

    public function authorize()
    {
        // instantiate a Guzzle client
        $client = new \GuzzleHttp\Client();

        // force to use a specific entry in the access database
        // TODO update to use the current login
        $form_params = [
            'client_id' => config($this->getRestConfigPath() . '.client-id'),
            'client_secret' => config($this->getRestConfigPath() . '.client-secret'),
            'grant_type' => 'client_credentials',
            'scope' => '*'
        ];
        // parse the API_BASE_URL for just the host name
        $parsed = parse_url(config($this->getRestConfigPath() . '.base-url'));
        $request_url = $parsed['scheme'].'://'.$parsed['host'] . config($this->getRestConfigPath() . '.auth-uri');

        // accommodate for various development
        $verify = false;
        $verify = $this->getCertificates();

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
        session()->put($this->getService().'.api.' . self::TOKEN_TYPE, $response_array[self::TOKEN_TYPE] ?? null);
        session()->put($this->getService().'.api.' . self::ACCESS_TOKEN, $response_array[self::ACCESS_TOKEN] ?? null);

        // save the newly obtained token in a file
        if (!file_exists($tmp = $this->getTokenStorage() )) {
            $folder = substr($tmp,0,strrpos($tmp,'/'));
            if (!file_exists($folder)) {
                mkdir($folder);
            }
        }

        file_put_contents($this->getTokenStorage(), serialize($response_array));

        return true;
    }

    public function isFresh()
    {
        // pass through if token was previously saved and it hasn't expired
        $savedFilename = $this->getTokenStorage();

        if (file_exists($savedFilename)) {
            $saved_apitoken = unserialize(file_get_contents($savedFilename));
            $current_ut = time();
            if ($current_ut<$saved_apitoken[self::EXPIRES_AT]) {
                session()->put($this->getService().'.api.' . self::TOKEN_TYPE, $saved_apitoken[self::TOKEN_TYPE] ?? null);
                session()->put($this->getService().'.api.' . self::ACCESS_TOKEN, $saved_apitoken[self::ACCESS_TOKEN] ?? null);
                return true;
            }
        }
        return false;
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
    public function get($url)
    {
        $response = null;
        $opts = ['verify' => $this->getCertificates(),'headers' => $this->getHeaders()];
        $response = $this->httpClient->get($url,$opts);
        return json_decode($response->getBody());
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
        return json_decode($response->getBody());
    }

    /**
     * @return mixed
     */
    private function getTokenStorage()
    {
        return storage_path() . '/' . $this->getService().'/'.self::APITOKEN;
    }
    /**
     * @return mixed
     */
    private function getRestConfigPath()
    {
        return OauthClient::CONFIG_PATH . '.rest-api.' . $this->getService();
    }

}
