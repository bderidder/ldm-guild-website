<?php

namespace LaDanse\SiteBundle\Tests\Functional;

use LaDanse\SiteBundle\Tests\Controller\LaDanseTestBase;

use LaDanse\SiteBundle\Tests\Controller\AccountConst;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RouterSmokeTest extends LaDanseTestBase
{
    /**
     * @param string $url
     *
     * @dataProvider anonymousUrlProvider
     */
    public function testAnonymousPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->followRedirects();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @param string $url
     *
     * @dataProvider authenticatedUrlProvider
     */
    public function testAuthenticatedPageIsSuccessful($url)
    {
        $client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => AccountConst::USER1_USERNAME,
                'PHP_AUTH_PW'   => AccountConst::USER1_PASSWORD,
            )
        );
        $client->followRedirects();

        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @param string $url
     *
     * @dataProvider authenticatedUrlProvider
     */
    public function testAuthenticatedPageIsFailed($url)
    {
        $client = self::createClient();
        $client->followRedirects();

        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function anonymousUrlProvider()
    {
        return array(
            array('/'),
            array('/registration/'),
            array('/privacy/'),
            array('/about/'),
        );
    }

    public function authenticatedUrlProvider()
    {
        return array(
            array('/calendar/'),
            array('/events/'),
            array('/settings/password'),
            array('/settings/calExport'),
            array('/settings/resetSecret'),
            array('/settings/notifications'),
            array('/settings/profile'),
            array('/settings/'),
            array('/claims/create'),
            array('/claims/'),
            array('/teamspeak/clients'),
            array('/teamspeak/channels'),
            array('/teamspeak/'),
            array('/gallery/'),
            array('/forum/'),
            array('/help/'),
            array('/menu/'),
            array('/feedback/create')
        );
    }
}