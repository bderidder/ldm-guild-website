<?php

namespace LaDanse\SiteBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Client;

abstract class MemberTestBase extends LaDanseTestBase
{
    /**
     * Test if the welcome page shows the "more information" button
     *
     * @return void
     */
    public function testLoginRedirect()
    {
        $client = static::createClient();

        $client->request('GET', $this->getUrl($client));

        $this->assertTrue($client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/login$/', $client->getResponse()->headers->get('location'));

        $crawler = $client->followRedirect();

        $this->assertTrue(
            $crawler->filter('html:contains("Sign In")')->count() == 1
        );

        $this->assertTrue(
            $crawler->filter('html:contains("I forgot my password")')->count() == 1
        );
    }

    /**
     * Test if the welcome page contains the username in Javascript
     *
     * @return void
     */
    public function testUsernameInJS()
    {
        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => AccountConst::USER1_USERNAME,
                'PHP_AUTH_PW'   => AccountConst::USER1_PASSWORD,
            )
        );

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $crawler = $client->request('GET', $this->getUrl($client));

        $this->assertTrue(
            $crawler->filter(
                'html:contains("username: \'' . AccountConst::USER1_USERNAME . '\'")'
            )->count() == 1
        );

        $this->assertTrue(
            $crawler->filter(
                'html:contains("displayName: \'' . AccountConst::USER1_DISPLAY . '\'")'
            )->count() == 1
        );
    }
}
