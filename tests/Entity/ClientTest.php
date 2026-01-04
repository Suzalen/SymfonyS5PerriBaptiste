<?php

namespace App\Tests\Entity;

use App\Entity\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testClientCreation()
    {
        $client = new Client();
        $client->setFirstname('John');
        $client->setLastname('Doe');
        $client->setEmail('john.doe@example.com');
        $client->setPhoneNumber('1234567890');
        $client->setAddress('123 Main St');

        $this->assertEquals('John', $client->getFirstname());
        $this->assertEquals('Doe', $client->getLastname());
        $this->assertEquals('john.doe@example.com', $client->getEmail());
        $this->assertNotNull($client->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $client->getCreatedAt());
    }
}
