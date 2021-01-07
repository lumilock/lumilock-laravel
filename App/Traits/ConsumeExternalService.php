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
                'http_errors' => false
            ]);

            if (isset($this->secret)) {
                $headers['Authorization_secret'] = $this->secret;
            }
            $startTime = microtime(true);
            $promise = $client->requestAsync($method, $requestUrl, [
                'form_params' => $formParams,
                'headers'     => $headers,
                'synchronous' => false,
                'timeout' => 10
            ]);

            $responseHolder = null;
            $promise->then(function ($response) use (&$responseHolder,$startTime) {
                dd('Got a response! ' . $response->getStatusCode());
                dd("response: ". number_format(microtime(true) - $startTime,4));
                $responseHolder = $response;
            });
            // dd("->then() function setup: ". number_format(microtime(true) - $startTime,4));
            $queue = \GuzzleHttp\Promise\queue();
            $queue->run();
            $queue->run();
            $queue->run();
            $queue->run();
            $queue->run();
            $queue->run();
            $queue->run();
            $queue->run();
            $queue->run();
            // dd("run run run: ". number_format(microtime(true) - $startTime,4));
            sleep(2);
            // dd("sleep: ". number_format(microtime(true) - $startTime,4));
            $promise->wait();
            // dd("wait: ". number_format(microtime(true) - $startTime,4));
            $results = (string)$responseHolder->getBody();

            // $this->dump($results);
            $duration = microtime(true) - $startTime;

            // $this->dump("Total duration: $duration");
            // return $response->wait()->getBody()->getContents();
            return 'hello';
            //https://github.com/guzzle/guzzle/issues/1127
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
