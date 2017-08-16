<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorsControllerTest extends WebTestCase
{
	public function testIndex()
	{
		$client = static::createClient();

		$container = $client->getContainer();

		$crawler = $client->request('GET', '/authors', [], [], [
			'PHP_AUTH_USER' => 'testuser',
			'PHP_AUTH_PW'   => $container->getParameter('password')
		]);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());

		$this->assertContains('Фильтр', $crawler->filter('body')->text());
	}
}
