<?php

declare(strict_types=1);

use Jwp\Exception\AuthenticationException;
use Jwp\Exception\ClientException;
use PHPUnit\Framework\TestCase;


final class ClientTest extends TestCase
{

  private function getDevAuth(): Jwp\Auth
  {
    return new Jwp\Auth('dev', 'meXxp1xABjiy5skBF9ecnwDBePPqMeIL80hBgHaiHT54yroKKyVZFffb459jLFyi');
  }

  private function getBadAuth(): Jwp\Auth
  {
    return new Jwp\Auth('xxx', 'xxx');
  }

  public function test401Unauthorized(): void
  {
    $this->expectException(AuthenticationException::class);
    $client = new Jwp\Client($this->getBadAuth());
    $client->connect();
  }

  public function testCanConnectToDevServer(): void
  {
    $auth = $this->getDevAuth();
    $client = new Jwp\Client($auth);
    $resp = $client->connect();
    $this->assertArrayHasKey('app_id', $resp);
    $this->assertArrayHasKey('connect_token', $resp);
  }

  public function testCanConnectToDevServerWithAChannelList(): void
  {
    $auth = $this->getDevAuth();
    $client = new Jwp\Client($auth);
    $resp = $client->connect(['channels' => ['chan1', 'chan2']]);
    $this->assertArrayHasKey('app_id', $resp);
    $this->assertArrayHasKey('connect_token', $resp);
  }

  public function testCanConnectToDevServerWithPerChannelConfiguration(): void
  {
    $auth = $this->getDevAuth();
    $client = new Jwp\Client($auth);
    $resp = $client->connect(['channels' => [
      // Empty configuration
      'chan1' => (object) [],
      'chan2' =>  ['presence_track' => true]
      // Unknown fields are ignore
    ]]);
    $this->assertArrayHasKey('app_id', $resp);
    $this->assertArrayHasKey('connect_token', $resp);
  }

  public function testCannotConnectToDevServerWithBadChannelConfigurationValue(): void
  {
    $auth = $this->getDevAuth();
    $client = new Jwp\Client($auth);
    $this->expectException(ClientException::class);
    // `true` is not a valid configuration
    $client->connect(['channels' => ['chan1' => true]]);
  }

  public function testCannotConnectToDevServerWithBadChannelConfigurationKey(): void
  {
    $auth = $this->getDevAuth();
    $client = new Jwp\Client($auth);
    $this->expectException(ClientException::class);
    $client->connect(['channels' => ['chan1' => ['some_bad_key' => 'hello']]]);
  }
}
