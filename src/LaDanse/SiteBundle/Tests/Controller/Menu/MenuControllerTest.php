<?php

namespace LaDanse\SiteBundle\Tests\Controller\Menu;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use LaDanse\SiteBundle\Tests\Controller\AccountConst;

/**
 * Class DefaultControllerTest
 *
 * @category TestCase
 * @package  LaDanse\SiteBundle\Tests\Controller
 * @author   Bavo De Ridder <bavo@coderspotting.org>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */
class MenuControllerTest extends WebTestCase
{
    const CONTROLLER_URL = "/menu/";

    /**
     * Test if the welcome page shows the "more information" button
     *
     * @return void
     */
    public function testLoginRedirect()
    {
        $client = static::createClient();

        $client->request('GET', MenuControllerTest::CONTROLLER_URL);

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

        $crawler = $client->request('GET', MenuControllerTest::CONTROLLER_URL);

        $this->assertTrue(
            $crawler->filter('html:contains("My Characters")')->count() > 0
        );

        $this->assertTrue(
            $crawler->filter('html:contains("Pictures")')->count() > 0
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

        $crawler = $client->request('GET', MenuControllerTest::CONTROLLER_URL);

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
