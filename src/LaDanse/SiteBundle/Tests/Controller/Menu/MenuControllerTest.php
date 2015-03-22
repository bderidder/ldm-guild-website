<?php

namespace LaDanse\SiteBundle\Tests\Controller\Menu;

use LaDanse\SiteBundle\Tests\Controller\MemberTestBase;

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
class MenuControllerTest extends MemberTestBase
{
    const CONTROLLER_URL = "/menu/";

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

        $crawler = $client->request('GET', $this->getUrl($client));

        $this->assertTrue(
            $crawler->filter('html:contains("My Characters")')->count() > 0
        );

        $this->assertTrue(
            $crawler->filter('html:contains("Pictures")')->count() > 0
        );
    }

    protected function getUrl($client)
    {
        return $this->generateUrl($client, "menuIndex");
    }
}
