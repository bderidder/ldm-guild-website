<?php

namespace LaDanse\SiteBundle\Tests\Functional\Controller\Welcome;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @group FunctionalTest
 */
class WelcomeControllerTest extends WebTestCase
{
    /**
     * Test if the welcome page shows the "more information" button
     *
     * @return void
     */
    public function testMoreInformationButton()
    {
        $client = static::makeClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        try
        {
            $crawler = $client->request('GET', '/');

            $this->assertTrue(
                $crawler->filter('html:contains("more information")')->count() > 0
            );
        }
        catch(\Exception $e)
        {
            $this->fail('Exception while executing request ' . $e->getMessage());
        }
    }

    /**
     * Test if the welcome page shows the "login or register" button
     * when not logged in
     *
     * @return void
     */
    public function testGuest()
    {
        $client = static::makeClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        try
        {
            $crawler = $client->request('GET', '/');

            $this->assertTrue(
                $crawler->filter('html:contains("login or register")')->count() > 0
            );

            $this->assertTrue(
                $crawler->filter('html:contains("member section")')->count() == 0
            );
        }
        catch (\Exception $e)
        {
            $this->fail('Exception while executing request ' . $e->getMessage());
        }
    }

    /**
     * Test if requesting / with a logged-in user gives menu
     *
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

        $client->followRedirects(false);

        $client->request('GET', '/');

        $this->assertTrue(
            $client->getResponse() instanceof RedirectResponse,
            'Response was not a redirect response'
        );

        $this->assertRegExp('/\/menu\/$/', $client->getResponse()->headers->get('location'));
    }
}
