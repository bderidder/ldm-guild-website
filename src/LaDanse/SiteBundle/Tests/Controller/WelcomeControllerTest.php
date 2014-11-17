<?php

namespace LaDanse\SiteBundle\Tests\Controller;

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
class WelcomeControllerTest extends WebTestCase
{
    /**
     * Test if the welcome page shows the "more information" button
     *
     * @return void
     */
    public function testMoreInformationButton()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue(
            $crawler->filter('html:contains("more information")')->count() > 0
        );
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

        $crawler = $client->request('GET', '/');

        $this->assertTrue(
            $crawler->filter('html:contains("login or register")')->count() > 0
        );

        $this->assertTrue(
            $crawler->filter('html:contains("member section")')->count() == 0
        );
    }

    /**
     * Test if the welcome page shows the "login or register" button
     * when not logged in
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

        $crawler = $client->request('GET', '/');

        $this->assertTrue(
            $crawler->filter('html:contains("member section")')->count() > 0
        );

        $this->assertTrue(
            $crawler->filter('html:contains("login or register")')->count() == 0
        );
    }

    /**
     * Test if the welcome page contains the username in Javascript
     * when not logged in
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

        $crawler = $client->request('GET', '/');

        $this->assertTrue(
            $crawler->filter(
                'html:contains("username: \'' . AccountConst::USER1_DISPLAY . '\'")'
            )->count() == 1
        );
    }
}
