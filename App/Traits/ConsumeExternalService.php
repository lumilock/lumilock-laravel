<?php

namespace lumilock\lumilock\App\Traits;

use GuzzleHttp\Client;

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
        $client = new Client([
            'base_uri'  =>  $this->baseUri,
            'http_errors' => false
        ]);

        if(isset($this->secret))
        {
            $headers['Authorization_secret'] = $this->secret;
            $headers['Authorization'] = "Bearer:465fsd46fd5s21fdsfsd312sfd45f6sd465";
        }
        $response = $client->request($method, $requestUrl, [
            'form_params' => $formParams,
            'headers'     => $headers,
        ]);
        return $response->getBody()->getContents();
    }
}