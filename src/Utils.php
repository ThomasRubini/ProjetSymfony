<?php

namespace App;
use GuzzleHttp\Client;

class Utils
{
    public static function makeRequest(string $method, string $endpoint, array $querryParams)
    {
        // Create a new Guzzle HTTP client
        $baseUri = 'https://api.discogs.com/';
        $client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 10, // You can set request timeout (in seconds)
        ]);

        // Set the base URI for the request and any default request options
        $auth = array(
            "key" => "kLzxeHuJCXRyQuBURaEN",
            "secret" => "biDCkuoMbGLeDtQGqCPCtbVqmzPaVmQG"
        );

        // Create the query parameters array
        $querryParams = array_merge($querryParams, $auth);

        // Send a GET request to the specified URI with the query parameters
        $response = $client->request($method, $endpoint, [
            'query' => $querryParams
        ]);

        // Get the status code of the response
        $statusCode = $response->getStatusCode();

        // Get the body of the response
        $body = $response->getBody()->getContents();

        return json_decode($body, true);
    }
}
