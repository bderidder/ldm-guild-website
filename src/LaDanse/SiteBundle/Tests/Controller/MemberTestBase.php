<?php

namespace LaDanse\SiteBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 *
 * @category TestCase
 * @package  LaDanse\SiteBundle\Tests\Controller
 * @author   Bavo De Ridder <bavo@coderspotting.org>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */
class MemberTestBase extends WebTestCase
{
    /**
     * Test if the welcome page shows the "more information" button
     *
     * @return void
     */
    public function testLoginRedirect()
    {
        $client = static::createClient();

        $client->request('GET', $this->getUrl());

        $this->assertTrue($client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/$/', $client->getResponse()->headers->get('location'));

        $crawler = $client->followRedirect();

        $this->assertTrue(
            $crawler->filter('html:contains("login or register")')->count() > 0
        );

        $this->assertTrue(
            $crawler->filter('html:contains("member section")')->count() == 0
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

        $client->followRedirects();

        $crawler = $client->request('GET', $this->getUrl());

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

    protected function getUrl()
    {
        return '';
    }
}
