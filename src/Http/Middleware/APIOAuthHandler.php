<?php

namespace Unmit\ldk\Http\Middleware;

use Closure;

/**
 * Class: APIOAuthHandler
 *
 * @author Michael Han <mhan1@unm.edu>
 * @author Ronald V Estrada <rvestra@unm.edu>
 *
 * @version 0.1.0 initial write-up 2019-10-23 MH
 * @since 0.2.1
 */
class APIOAuthHandler
{
    const APITOKEN = 'apitoken';
    const TOKEN_TYPE ='token_type';
    const ACCESS_TOKEN ='access_token';
    const OAUTH_URI = '/oauth/token';
    const EXPIRES_AT = 'expires_at';
    const API_CONFIG_PATH = 'app-extra.rest-api';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // pass through if token has been obtained already
        if (session()->has('api.' . self::ACCESS_TOKEN)) {
            return $next($request);
        }

        // pass through if token was previously saved and it hasn't expired
        if (file_exists(storage_path() . '/' . self::APITOKEN)) {
            $saved_apitoken = unserialize(file_get_contents(storage_path() . '/' . self::APITOKEN));
            $current_ut = time();
            if ($current_ut<$saved_apitoken[self::EXPIRES_AT]) {
                session()->push('api.' . self::TOKEN_TYPE, $saved_apitoken[self::TOKEN_TYPE] ?? null);
                session()->push('api.' . self::ACCESS_TOKEN, $saved_apitoken[self::ACCESS_TOKEN] ?? null);
                return $next($request);
            }
        }

        // since a token has not been obtained for this session
        // get one through Guzzle client

        // instantiate a Guzzle client
        $client = new \GuzzleHttp\Client();

        // force to use a specific entry in the access database
        // TODO update to use the current login
        $form_params = [
            'client_id' => config(self::API_CONFIG_PATH . '.client-id'),
            'client_secret' => config(self::API_CONFIG_PATH . '.client-secret'),
            'grant_type' => 'client_credentials',
            'scope' => '*'
        ];

        // parse the API_BASE_URL for just the host name
        $parsed = parse_url(config(self::API_CONFIG_PATH.'.base-url'));
        $request_url = $parsed['scheme'].'://'.$parsed['host'] . self::OAUTH_URI;

        // make the request
        $response = $client->request(
            'POST',
            $request_url,
            [
                'form_params' => $form_params,
                // /*
                'verify'  => (((\App::environment(['local','staging'])) &&
                    (request()->getHttpHost()=='oraapi02d.unm.edu')) ?
                    ('/etc/ssl/certs/ssl_cert-key.pem'):'') // DEBUG added to accommodate for when application is running on oraapi02 server
                    // */
            ]
        );

        // get the response and store the returned values into session variables
        $response->getBody();
        $response_array = json_decode($response->getBody(),true);

        // don't confuse expires_in with the actual expiration time
        $response_array[self::EXPIRES_AT] = time() + $response_array['expires_in'];

        // store
        session()->push('api.' . self::TOKEN_TYPE, $response_array[self::TOKEN_TYPE] ?? null);
        session()->push('api.' . self::ACCESS_TOKEN, $response_array[self::ACCESS_TOKEN] ?? null);

        // save the newly obtained token in a file
        file_put_contents(storage_path() . '/' . self::APITOKEN, serialize($response_array));

        return $next($request);
    }
}
