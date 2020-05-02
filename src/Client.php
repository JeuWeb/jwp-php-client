<?php

declare(strict_types=1);

namespace Jwp;

use GuzzleHttp\Client as Http;
use GuzzleHttp\Psr7\Response;
use Jwp\Exception\AuthenticationException;
use Jwp\Exception\ClientException;
use Jwp\Exception\ServerException;

class Client
{
    private $http;
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->http = new Http([
            'base_uri' => 'http://localhost:4000',
            'timeout'  => 2.0,
            'headers' => $this->getHeaders(),
            'auth' => $auth->getBasic(),
        ]);
    }

    private function getHeaders()
    {
        return [
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];
    }

    private function post($url, $payload = [])
    {
        try {
            $response = $this->http->post($url, $payload);
            return $this->handleResponse($response);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->handleResponse($e->getResponse());
        }
    }

    private function handleResponse(Response $response)
    {
        $body = \GuzzleHttp\json_decode($response->getBody(), true);
        $status = array_key_exists('status', $body) ? $body['status'] : '__nostatus__';
        switch ($status) {
            case 'ok':
                return $body['data'] ?? null;
            case 'error':
                switch ($response->getStatusCode()) {
                    case 401:
                        throw new AuthenticationException('Unauthorized');
                    default:
                        throw new ClientException($body['error']['message']);
                }
            default:
                throw new ServerException('Unknown status value', $status);
        }
    }

    public function authenticateSocket(string $socketID, int $maxAge)
    {
        if ($maxAge > 60) {
            throw new ClientException("Max age value is too high: $maxAge");
        }

        $endTime = time() + $maxAge;
        $signature = $this->auth->sign("$socketID:$endTime");
        return "$socketID:$endTime:$signature";
    }

    public function authenticateChannel(string $socketID, string $channel, array $meta = null, array $options = [])
    {
        return $this->auth->signChannelAuth($socketID, $channel, ['meta' => (object) $meta, 'options' => (object) $options]);
    }

    public function push($channel, $event, $payload)
    {
        $json = json_encode([
            'channel' => $channel,
            'event' => $event,
            'payload' => $payload,
        ]);
        return $this->post('/api/v1/push', ['body' => $json]);
    }
}
