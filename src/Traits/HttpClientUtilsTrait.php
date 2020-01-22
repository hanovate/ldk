<?php

namespace Unmit\ldk\Traits;

/**
 * Trait: HttpClientUtilsTrait - This trait is to used with UNM API Model
 * objects to manage components/relationships with API
 *
 * @version 0.1.1 2019-09-23 MH
 * @since 0.1.0
 */
trait HttpClientUtilsTrait
{
    /**
     * Convert GuzzleHttp Client's response into an array.
     *
     * "This is the method that will be accessed from an API Model object
     * to establish an accessor like Laravel's Eloquent Relations" - RE
     *
     * @param GuzzleHttp\Psr7\Response $response
     *
     * @version 0.1.2 2019-09-23 MH
     * @since 0.1.1
     */
    protected function getArrayFromClientResponse($response)
    {
        // single result
        $results = null;

        $response_content = json_decode($response->getBody()->getContents(), true);

        $metadata_enabled = isset($response_content['data']) && isset($response_content['id']);

        $roughResult = $metadata_enabled ? $response_content['data']:$response_content;

        if (count($roughResult) == 1) {
            foreach($roughResult as $payload){
                $results = $payload;
                break;
            }
        } else {
            $results = $roughResult;
        }

        return $results;
    }

}
