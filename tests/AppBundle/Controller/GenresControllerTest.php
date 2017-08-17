<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenresControllerTest extends WebTestCase
{
	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Client
	 */
	private $client;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \Doctrine\ORM\EntityRepository $repo
	 */
	private $repo;

	/**
	 * GenresControllerTest constructor.
	 * @param null $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		$this->client = static::createClient();
		$this->client->followRedirects();
		$this->container = $this->client->getContainer();
		$this->repo = $this->container->get('doctrine')->getRepository(Genre::class);
	}

	/**
	 * @param string $uri
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	private function getCrawler($uri)
	{
		return $this->client->request('GET', $uri, [], [], [
			'PHP_AUTH_USER' => 'testuser',
			'PHP_AUTH_PW' => $this->container->getParameter('password')
		]);
	}

	public function testIndex()
	{
		$crawler = $this->getCrawler('/genres');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$form = $crawler->filter('.filter-form')->form();
		$form->setValues([
			"genre_filter[name]" => "ра"
		]);

		$this->client->submit($form);
		$response = $this->client->getResponse();

		$this->assertContains('литература', $response->getContent());
		$this->assertContains('Программирование', $response->getContent());
	}

	public function testAdd()
	{
		$crawler = $this->getCrawler('/genres/add');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$form = $crawler->filter('.genre-form')->form();

		$form->setValues([
			"genre[name]" => "test genre"
		]);

		$this->client->submit($form);
		$response = $this->client->getResponse();

		$this->assertContains('Жанр добавлен.', $response->getContent());
	}

	public function testEdit()
	{
		/**
		 * @var Genre
		 */
		$genre = $this->repo->findOneBy(['name' => 'test genre']);

		$this->assertNotFalse($genre instanceof Genre);

		$crawler = $this->getCrawler(sprintf("/genres/edit/%s", $genre->getId()));

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$form = $crawler->filter('.genre-form')->form();

		$form->setValues([
			"genre[name]" => "test genre 1"
		]);

		$this->client->submit($form);
		$response = $this->client->getResponse();

		$this->assertContains('Изменения сохранены.', $response->getContent());
	}

	public function testDelete()
	{
		/**
		 * @var Genre
		 */
		$genre = $this->repo->findOneBy(['name' => 'test genre 1']);

		$this->assertNotFalse($genre instanceof Genre);

		$crawler = $this->getCrawler(sprintf("/genres/delete/%s", $genre->getId()));

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$this->assertContains('Жанр удален.', $this->client->getResponse()->getContent());
	}
}
