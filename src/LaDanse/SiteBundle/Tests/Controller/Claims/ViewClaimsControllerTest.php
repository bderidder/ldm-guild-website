<?php

namespace LaDanse\SiteBundle\Tests\Controller\Claims;

use LaDanse\SiteBundle\Tests\Controller\MemberTestBase;

use LaDanse\SiteBundle\Tests\Controller\AccountConst;

class ViewClaimsControllerTest extends MemberTestBase
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

        $crawler = $client->request('GET', $this->getUrl($client));

        $this->assertTrue(
            $crawler->filter('html:contains("Below you can find the list of")')->count() == 1
        );

        $this->assertTrue(
            $crawler->filter('html:contains("add a character")')->count() == 1
        );
    }

    protected function getUrl($client)
    {
        return $this->generateUrl($client, "viewClaims");
    }
}