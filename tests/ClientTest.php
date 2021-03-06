<?php

declare(strict_types=1);

use Jwp\Auth;
use Jwp\Client;
use Jwp\Exception\AuthenticationException;
use Jwp\Exception\ClientException;
use PHPUnit\Framework\TestCase;


final class ClientTest extends TestCase
{



  // private function getDevAuth(): Jwp\Auth
  // {
  //   return new Jwp\Auth('dev', 'meXxp1xABjiy5skBF9ecnwDBePPqMeIL80hBgHaiHT54yroKKyVZFffb459jLFyi');
  // }

  // private function getBadAuth(): Jwp\Auth
  // {
  //   return new Jwp\Auth('xxx', 'xxx');
  // }

  // public function test401Unauthorized(): void
  // {
  //   $this->expectException(AuthenticationException::class);
  //   $client = new Jwp\Client($this->getBadAuth());
  //   $client->connect();
  // }

  // public function testCanConnectToDevServer(): void
  // {
  //   $auth = $this->getDevAuth();
  //   $client = new Jwp\Client($auth);
  //   $resp = $client->connect();
  //   $this->assertArrayHasKey('app_id', $resp);
  //   $this->assertArrayHasKey('connect_token', $resp);
  // }

  // public function testCanConnectToDevServerWithAChannelList(): void
  // {
  //   $auth = $this->getDevAuth();
  //   $client = new Jwp\Client($auth);
  //   $resp = $client->connect(['socket_id' => '123', 'channels' => ['chan1', 'chan2']]);
  //   $this->assertArrayHasKey('app_id', $resp);
  //   $this->assertArrayHasKey('connect_token', $resp);
  // }

  // public function testCanConnectToDevServerWithPerChannelConfiguration(): void
  // {
  //   $auth = $this->getDevAuth();
  //   $client = new Jwp\Client($auth);
  //   $resp = $client->connect(['channels' => [
  //     // Empty configuration
  //     'chan1' => (object) [],
  //     'chan2' =>  ['presence_track' => true]
  //     // Unknown fields are ignore
  //   ]]);
  //   $this->assertArrayHasKey('app_id', $resp);
  //   $this->assertArrayHasKey('connect_token', $resp);
  // }

  // public function testCannotConnectToDevServerWithBadChannelConfigurationValue(): void
  // {
  //   $auth = $this->getDevAuth();
  //   $client = new Jwp\Client($auth);
  //   $this->expectException(ClientException::class);
  //   // `true` is not a valid configuration
  //   $client->connect(['channels' => ['chan1' => true]]);
  // }

  // public function testCannotConnectToDevServerWithBadChannelConfigurationKey(): void
  // {
  //   $auth = $this->getDevAuth();
  //   $client = new Jwp\Client($auth);
  //   $this->expectException(ClientException::class);
  //   $client->connect(['channels' => ['chan1' => ['some_bad_key' => 'hello']]]);
  // }

  private function getDevAuth()
  {
    $appID = "dev";
    $key = "some-key";
    $secret = "$3CR3T";
    return new Auth($appID, $key, $secret);
  }

  public function testCanAuthenticateSocket()
  {
    $jwp = new Client($this->getDevAuth());
    $token = $jwp->authenticateSocket('user:123', 1000);

    $this->assertIsString($token);
  }

  public function testCanAuthenticateChannelOptions()
  {
    $jwp = new Client($this->getDevAuth());

    $meta = ['username' => 'Sephi-Chan'];
    $auth = $jwp->authenticateChannel('user:123', 'general', $meta);

    $this->assertEquals($auth['auth'], 'EDBAB7A00022884C05F35DE8C9BF76C5EC06B1865A49194F63918F6531C4496F');
    $this->assertEquals($meta, json_decode($auth['data'], true)['meta']);
  }
}
