<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenresControllerTest extends WebTestCase
{
	const CLASS_NAME = 'AppBundle\Controller\GenresController';

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
	 * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
	 */
	private $translator;

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
		$this->translator = $this->container->get('translator.default');
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

	/**
	 * @param string $method
	 * @return string
	 */
	private function getFullMethodName($method)
	{
		return sprintf("%s::%s", self::CLASS_NAME, $method);
	}

	public function testIndex()
	{
		$crawler = $this->getCrawler('/genres');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
		$this->assertEquals($this->getFullMethodName('indexAction'), $this->client->getRequest()->attributes->get('_controller'));

		$form = $crawler->filter('.filter-form')->form();
		$form->setValues([
			"genre_filter[name]" => "ра"
		]);

		$this->client->submit($form);

		$content = $this->client->getResponse()->getContent();

		$this->assertContains('литература', $content);
		$this->assertContains('Программирование', $content);
		$this->assertNotContains('Ужасы', $content);
	}

	public function testAdd()
	{
		$crawler = $this->getCrawler('/genres/add');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
		$this->assertEquals($this->getFullMethodName('addAction'), $this->client->getRequest()->attributes->get('_controller'));

		$form = $crawler->filter('.genre-form')->form();

		$form->setValues([
			"genre[name]" => "test genre"
		]);

		$this->client->submit($form);
		$response = $this->client->getResponse();

		$this->assertContains($this->translator->trans('messages.genre_added'), $response->getContent());
	}

	public function testAddNotUnique()
	{
		$crawler = $this->getCrawler('/genres/add');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
		$this->assertEquals($this->getFullMethodName('addAction'), $this->client->getRequest()->attributes->get('_controller'));

		$form = $crawler->filter('.genre-form')->form();

		$form->setValues([
			"genre[name]" => "test genre"
		]);

		$this->client->submit($form);
		$response = $this->client->getResponse();

		$this->assertContains($this->translator->trans('genre.unique', [], 'validators'), $response->getContent());
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
		$this->assertEquals($this->getFullMethodName('editAction'), $this->client->getRequest()->attributes->get('_controller'));

		$form = $crawler->filter('.genre-form')->form();

		$form->setValues([
			"genre[name]" => "test genre 1"
		]);

		$this->client->submit($form);
		$response = $this->client->getResponse();

		$this->assertContains($this->translator->trans('messages.changes_accepted'), $response->getContent());
	}

	public function testDelete()
	{
		/**
		 * @var Genre
		 */
		$genre = $this->repo->findOneBy(['name' => 'test genre 1']);

		$this->assertNotFalse($genre instanceof Genre);

		$crawler = $this->getCrawler(sprintf("/genres/delete/%s", $genre->getId()));

		$response = $this->client->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals($this->getFullMethodName('indexAction'), $this->client->getRequest()->attributes->get('_controller'));

		$this->assertContains($this->translator->trans('messages.genre_deleted'), $response->getContent());
	}
}
