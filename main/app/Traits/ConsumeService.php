<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

trait ConsumeService
{
    /**
     * Send a request to any service
     * @return string
     */
    public function performRequest($method, $requestUrl, $formParams = [], $headers = [])
    {
        switch ($method) {
            case 'GET':
                $response = Http::get($this->baseUri . $requestUrl, ['queryOptions' => $formParams, 'token' => $this->token]);
                break;
            default:
                break;
        }
        return $response;
    }
}
