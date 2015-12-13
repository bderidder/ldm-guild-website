<?php

namespace LaDanse\SiteBundle\Tests\Functional\Controller\Menu;

use LaDanse\SiteBundle\Tests\Functional\Controller\MemberTestBase;

use LaDanse\SiteBundle\Tests\Functional\Controller\AccountConst;
use Symfony\Bundle\FrameworkBundle\Client;

class MenuControllerTest extends MemberTestBase
{
    /**
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

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        try
        {
            $crawler = $client->request('GET', $this->getUrl($client));

            $this->assertTrue(
                $crawler->filter('html:contains("My WoW Characters")')->count() > 0
            );

            $this->assertTrue(
                $crawler->filter('html:contains("Pictures")')->count() > 0
            );
        }
        catch(\Exception $e)
        {
            $this->fail('Exception while executing request ' . $e->getMessage());
        }
    }

    protected function getUrl(Client $client, $parameters = array())
    {
        return $this->generateUrl($client, "menuIndex");
    }
}
