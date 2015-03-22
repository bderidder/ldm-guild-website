<?php

namespace LaDanse\SiteBundle\Tests\Controller\About;

use LaDanse\SiteBundle\Tests\Controller\LaDanseTestBase;

use LaDanse\SiteBundle\Tests\Controller\AccountConst;

class AboutControllerTest extends LaDanseTestBase
{
    /**
     * @return void
     */
    public function testGuest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $this->getUrl($client));

        $this->assertTrue(
            $crawler->filter('html:contains("About La Danse Macabre")')->count() > 0
        );
    }

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
            $crawler->filter('html:contains("About La Danse Macabre")')->count() > 0
        );
    }

    protected function getUrl($client)
    {
        return $this->generateUrl($client, 'aboutIndex');
    }
}
