<?php

namespace lumilock\lumilock\App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;

trait ConsumeExternalService
{
    /**
     * Send request to any service
     * @param $method
     * @param $requestUrl
     * @param array $formParams
     * @param array $headers
     * @return string
     */
    public function performRequest($method, $requestUrl, $formParams = [], $headers = [])
    {
        try {
            $client = new Client([
                'base_uri'  =>  $this->baseUri,
                'http_errors' => false,
                'headers' => ['Connection' => 'close'], // Or simply add this to the request object
                'CURLOPT_FORBID_REUSE' => true,
                'CURLOPT_FRESH_CONNECT' => true,
            ]);
            if (isset($this->secret) && $this->secret !== '') {
                $headers['Authorization_secret'] = $this->secret;
            }

            $promise = $client->requestAsync($method, $requestUrl, [
                'form_params' => $formParams,
                'headers'     => $headers,
                'synchronous' => false,
                'timeout' => 10
            ]);
            $response = $promise->wait();
            return ["content" => $response->getBody()->getContents(), "status" => $response->getStatusCode()];
        } catch (\Exception $e) {
            return response()->json(
                [
                    'data' => $e,
                    'status' => 'CONNECTION_REFUSED',
                    'message' => $e
                ],
                503
            );
        }
    }
}
