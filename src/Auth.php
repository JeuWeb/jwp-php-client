<?php

declare(strict_types=1);

namespace Jwp;

use GuzzleHttp\Client as Http;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Auth
{

    private $appID;
    private $apiKey;
    private $secret;

    public function __construct($appID, $apiKey, $secret)
    {
        $this->appID = $appID;
        $this->apiKey = $apiKey;
        $this->secret = $secret;
    }


    public function getBasic()
    {
        return [$this->appID, $this->apiKey];
    }

    public function sign(string $value)
    {
        return strtoupper(hash_hmac('sha256', $value, $this->secret));
    }

    public function signChannelAuth(string $socketID, string $channel, $data = null): array
    {
        // appID is only required to be given to the JS client library in order
        // to create the full topic name – useful for async params generation.
        $auth = ['app_id' => $this->appID];
        $signature = null;

        if ($data !== null) {
            $dataAsJson = json_encode($data);
            $auth['auth'] = $this->sign("$socketID:$channel:$dataAsJson");
            $auth['data'] = $dataAsJson;
        } else {
            $auth['auth'] = $this->sign("$socketID:$channel");
            $auth['data'] = null;
        }

        return $auth;
    }
}
