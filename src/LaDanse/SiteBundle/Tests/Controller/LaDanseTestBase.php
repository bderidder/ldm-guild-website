<?php

namespace LaDanse\SiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class LaDanseTestBase extends WebTestCase
{
    /**
     * @param $client Client
     * @param $route string
     * @param array $parameters
     *
     * @return string
     */
    protected function generateUrl(Client $client, $route, $parameters = array())
    {
        return $client->getContainer()->get( 'router' )->generate( $route, $parameters );
    }

    /**
     * @param Client $client
     * @param array $parameters
     *
     * @return string
     */
    abstract protected function getUrl(Client $client, $parameters = array());
}
