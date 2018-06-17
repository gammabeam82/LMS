<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthorsControllerTest extends WebTestCase
{
    const CLASS_NAME = 'AppBundle\Controller\AuthorsController';

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
        $this->repo = $this->container->get('doctrine')->getRepository(Author::class);
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

    public function testList()
    {
        $crawler = $this->getCrawler('/authors');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($this->getFullMethodName('listAction'), $this->client->getRequest()->attributes->get('_controller'));

        $form = $crawler->filter('.filter-form')->form();
        $form->setValues([
            "author_filter[lastName]" => "ро"
        ]);

        $this->client->submit($form);

        $content = $this->client->getResponse()->getContent();

        $this->assertContains('Сорокин', $content);
        $this->assertContains('Стросс', $content);
        $this->assertNotContains('Уилсон', $content);
    }

    public function testAdd()
    {
        $crawler = $this->getCrawler('/authors/add');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($this->getFullMethodName('addAction'), $this->client->getRequest()->attributes->get('_controller'));

        $form = $crawler->filter('.author-form')->form();

        $form->setValues([
            "author[firstName]" => "Никифор",
            "author[lastName]" => "Ляпис-Трубецкой"
        ]);

        $this->client->submit($form);
        $content = $this->client->getResponse()->getContent();

        $this->assertContains($this->translator->trans('messages.author_added'), $content);
        $this->assertContains('Никифор Ляпис-Трубецкой', $content);
    }

    public function testAddEmpty()
    {
        $crawler = $this->getCrawler('/authors/add');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($this->getFullMethodName('addAction'), $this->client->getRequest()->attributes->get('_controller'));

        $form = $crawler->filter('.author-form')->form();

        $form->setValues([
            "author[firstName]" => "",
            "author[lastName]" => ""
        ]);

        $this->client->submit($form);

        $this->assertContains($this->translator->trans('blank', [], 'validators'), $this->client->getResponse()->getContent());
    }

    public function testEdit()
    {
        /**
         * @var Author
         */
        $author = $this->repo->findOneBy([
            'firstName' => 'Никифор',
            'lastName' => 'Ляпис-Трубецкой'
        ]);

        $this->assertNotFalse($author instanceof Author);

        $crawler = $this->getCrawler(sprintf("/authors/edit/%s", $author->getId()));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($this->getFullMethodName('editAction'), $this->client->getRequest()->attributes->get('_controller'));

        $form = $crawler->filter('.author-form')->form();

        $form->setValues([
            "author[firstName]" => "Никифоръ"
        ]);

        $this->client->submit($form);

        $this->assertContains($this->translator->trans('messages.changes_accepted'), $this->client->getResponse()->getContent());
    }

    public function testDelete()
    {
        /**
         * @var Author
         */
        $author = $this->repo->findOneBy([
            'firstName' => 'Никифоръ',
            'lastName' => 'Ляпис-Трубецкой'
        ]);

        $this->assertNotFalse($author instanceof Author);

        $crawler = $this->getCrawler(sprintf("/authors/delete/%s", $author->getId()));

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($this->getFullMethodName('listAction'), $this->client->getRequest()->attributes->get('_controller'));

        $this->assertContains($this->translator->trans('messages.author_deleted'), $response->getContent());
    }
}
