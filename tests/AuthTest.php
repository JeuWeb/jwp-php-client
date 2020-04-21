<?php

declare(strict_types=1);

use Jwp\Auth;
use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    private function getAuth()
    {
        $appID = "dev";
        $key = "some-key";
        $secret = "$3CR3T";
        return new Auth($appID, $key, $secret);
    }

    public function testTokenSigner(): void
    {
        $auth = $this->getAuth();
        $expected = 'B3AB90723B6139B98706E162E1DD63EBBF5EF2A8C5B2B1B384252C003ACB78E1';
        $signed = $auth->sign("Hello From 2020");
        $this->assertEquals($expected, $signed);
    }

    public function testSignChannel(): void
    {
        $auth = $this->getAuth();
        $authPayload = $auth->signChannelAuth("my-user", "my-channel");
        $this->assertEquals(
            '65A8C23A9963FFA0B17247CFA94C550636213AC6951B0D6BF9F1151D1BDB487C',
            $authPayload['auth']
        );


        $authPayload = $auth->signChannelAuth("my-user", "my-channel", ['some' => 'data', 'nums' => [1, 2, 3]]);
        $this->assertEquals(
            [
                'data' => '{"some":"data","nums":[1,2,3]}',
                'auth' => 'E00E7D0FFB87B6B24F1633DC2E9938086B99D1D1B7D2C080C4747DA9E60BCE4A',
            ],
            $authPayload
        );
    }
}
