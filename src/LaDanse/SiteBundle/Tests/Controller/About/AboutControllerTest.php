<?php

namespace LaDanse\SiteBundle\Tests\Controller\About;

use LaDanse\SiteBundle\Tests\Controller\LaDanseTestBase;

use LaDanse\SiteBundle\Tests\Controller\AccountConst;
use Symfony\Bundle\FrameworkBundle\Client;

class AboutControllerTest extends LaDanseTestBase
{
    /**
     * @return void
     */
    public function testGuest()
    {
        $client = static::createClient();

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

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

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $crawler = $client->request('GET', $this->getUrl($client));

        $this->assertTrue(
            $client->getResponse()->isSuccessful(),
            'Request was not succesful'
        );

        $this->assertTrue(
            $crawler->filter('html:contains("About La Danse Macabre")')->count() > 0,
            'Response did not contain string "About La Danse Macabre"'
        );
    }

    protected function getUrl(Client $client, $parameters = array())
    {
        return $this->generateUrl($client, 'aboutIndex');
    }
}
