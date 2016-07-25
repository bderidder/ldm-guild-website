<?php

namespace Tests\LaDanse\SiteBundle\Functional\Controller\Registration;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group FunctionalTest
 */
class RegistrationControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testGuest()
    {
        $client = static::makeClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $crawler = $client->request('GET', '/registration/');

        $this->assertTrue(
            $crawler->filter('html:contains("By registering your account you agree")')->count() > 0
        );
    }

    /**
     * @return void
     */
    public function testMember()
    {
        $fixtures = $this->loadFixtureFiles(array(
            'tests/LaDanse/SiteBundle/Fixtures/account.yml'
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

        $crawler = $client->request('GET', '/registration/');

        $this->assertTrue(
            $client->getResponse()->isSuccessful(),
            'Request was not succesful'
        );

        $this->assertTrue(
            $crawler->filter('html:contains("By registering your account you agree")')->count() > 0,
            'Response did not contain string "By registering your account you agree"'
        );
    }
}
