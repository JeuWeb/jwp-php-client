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

    public function __construct($appID, $apiKey)
    {
        $this->appID = $appID;
        $this->apiKey = $apiKey;
    }


    public function getBasic()
    {
        return [$this->appID, $this->apiKey];
    }
}
