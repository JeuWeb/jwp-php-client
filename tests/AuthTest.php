<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    public function testAuthCanBeRetrievedFromWrapper(): void
    {
        $auth = new Jwp\Auth('aaa', 'bbb');
        $this->assertEquals($auth->getBasic(), ['aaa', 'bbb']);
    }
}
