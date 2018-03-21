<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AppAvailabilityTest extends WebTestCase
{
    /**
     * @dataProvider getUrlsList
     */
    public function testRoutes($url)
    {
        $client = static::createClient();

        $client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'testuser',
            'PHP_AUTH_PW' => $client->getContainer()->getParameter('password')
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @return \Generator
     */
    public function getUrlsList()
    {
        yield ['/'];
        yield ['/archive'];
        yield ['/authors'];
        yield ['/authors/add'];
        yield ['/books'];
        yield ['/books/add'];
        yield ['/export'];
        yield ['/genres'];
        yield ['/genres/add'];
        yield ['/series'];
        yield ['/series/add'];
    }
}
