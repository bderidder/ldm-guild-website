<?php

namespace LaDanse\SiteBundle\Tests\Functional;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class RouterSmokeTest extends WebTestCase
{
    /**
     * @param string $url
     *
     * @dataProvider anonymousUrlProvider
     */
    public function testAnonymousPageIsSuccessful($url)
    {
        $client = self::makeClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $crawler = $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $crawler->filter('html:contains("Don\'t have a La Danse account?")')->count() == 0
        );
    }

    /**
     * @param string $url
     *
     * @dataProvider authenticatedUrlProvider
     */
    public function testAuthenticatedPageIsSuccessful($url)
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
        $client = self::makeClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

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
            array('/calendar'),
            array('/events'),
            array('/settings/password'),
            array('/settings/calExport'),
            array('/settings/resetSecret'),
            array('/settings/notifications'),
            array('/settings/profile'),
            array('/settings'),
            array('/claims/create'),
            array('/claims'),
            array('/teamspeak/clients'),
            array('/teamspeak/channels'),
            array('/teamspeak'),
            array('/gallery'),
            array('/forum'),
            array('/menu'),
            array('/feedback/create')
        );
    }
}