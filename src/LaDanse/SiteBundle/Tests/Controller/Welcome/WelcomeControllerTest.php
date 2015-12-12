<?php

namespace LaDanse\SiteBundle\Tests\Controller\Welcome;

use LaDanse\SiteBundle\Tests\Controller\LaDanseTestBase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\RedirectResponse;

use LaDanse\SiteBundle\Tests\Controller\AccountConst;

class WelcomeControllerTest extends LaDanseTestBase
{
    /**
     * Test if the welcome page shows the "more information" button
     *
     * @return void
     */
    public function testMoreInformationButton()
    {
        $client = static::createClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        try
        {
            $crawler = $client->request('GET', $this->getUrl($client));

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
        $client = static::createClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        try
        {
            $crawler = $client->request('GET', $this->getUrl($client));

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
        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => AccountConst::USER1_USERNAME,
                'PHP_AUTH_PW'   => AccountConst::USER1_PASSWORD,
            )
        );

        $client->followRedirects(false);

        $client->request('GET', $this->getUrl($client));

        $this->assertTrue(
            $client->getResponse() instanceof RedirectResponse,
            'Response was not a redirect response'
        );

        $this->assertRegExp('/\/menu\/$/', $client->getResponse()->headers->get('location'));
    }

    protected function getUrl(Client $client, $parameters = array())
    {
        return $this->generateUrl($client, "welcomeIndex");
    }
}
