<?php

namespace LaDanse\SiteBundle\Tests\Functional\Controller\TeamSpeak;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group FunctionalTest
 */
class TSProxyControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testClients()
    {
        $fixtures = $this->loadFixtureFiles(array(
            '@LaDanseSiteBundle/Tests/Fixtures/account.yml'
        ));

        /** @var Account $account */
        $account = $fixtures['mainAccount'];

        $client = static::makeClient(
            array(),
            array(
                'PHP_AUTH_USER' => $account->getUsername(),
                'PHP_AUTH_PW'   => 'test',
            )
        );

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $client->request('GET', '/teamspeak/clients');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @return void
     */
    public function testChannels()
    {
        $fixtures = $this->loadFixtureFiles(array(
            '@LaDanseSiteBundle/Tests/Fixtures/account.yml'
        ));

        /** @var Account $account */
        $account = $fixtures['mainAccount'];

        $client = static::makeClient(
            array(),
            array(
                'PHP_AUTH_USER' => $account->getUsername(),
                'PHP_AUTH_PW'   => 'test',
            )
        );

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $client->request('GET', '/teamspeak/channels');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
