<?php

namespace Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HTTPFoundation\Response;

class HomeControllerTest extends WebTestCase
{
  
  public function testShowPost()
  {
    $client = static::createClient();

    $client->request('GET', '/home');

    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }

}