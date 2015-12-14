<?php

namespace LaDanse\SiteBundle\Tests\Functional\Controller\About;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AboutControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testGuest()
    {
        $client = static::makeClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $crawler = $client->request('GET', '/about/');

        $this->assertTrue(
            $crawler->filter('html:contains("About La Danse Macabre")')->count() > 0
        );
    }

    /**
     * @return void
     */
    public function testMember()
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

        $crawler = $client->request('GET', '/about/');

        $this->assertTrue(
            $client->getResponse()->isSuccessful(),
            'Request was not succesful'
        );

        $this->assertTrue(
            $crawler->filter('html:contains("About La Danse Macabre")')->count() > 0,
            'Response did not contain string "About La Danse Macabre"'
        );
    }
}
