<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testPostScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/posts');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /post/");


    }
}
