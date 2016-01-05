<?php

class UserTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testUser()
    {
        $user = \Models\User::findFirst();
        $this->assertInstanceOf(\Models\User::class, $user);
    }
}
