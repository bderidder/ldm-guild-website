<?php

namespace Tests\LaDanse\SiteBundle\Functional\Controller\Settings;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @group FunctionalTest
 */
class SettingsIndexControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testRedirectToProfileSettings()
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

        $client->request('GET', '/settings/');

        $this->assertTrue(
            $client->getResponse() instanceof RedirectResponse,
            'Response was not a redirect response'
        );

        $this->assertRegExp('/\/settings\/profile$/', $client->getResponse()->headers->get('location'));
    }
}
