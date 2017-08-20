<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Client
	 */
	private $client;

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
	 */
	private $translator;

	/**
	 * DefaultControllerTest constructor.
	 * @param null $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		$this->client = static::createClient();
		$this->translator = $this->client->getContainer()->get('translator.default');
	}

	public function testIndex()
	{
		$crawler = $this->client->request('GET', '/');

		$response = $this->client->getResponse();

		$this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

		$this->assertEquals('AppBundle\Controller\DefaultController::indexAction', $this->client->getRequest()->attributes->get('_controller'));

		$this->assertContains($this->translator->trans('messages.recent_news'), $response->getContent());
	}
}
