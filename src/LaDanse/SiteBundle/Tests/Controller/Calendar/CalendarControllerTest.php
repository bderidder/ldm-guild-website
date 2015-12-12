<?php

namespace LaDanse\SiteBundle\Tests\Controller\Calendar;

use Symfony\Bundle\FrameworkBundle\Client;

use LaDanse\SiteBundle\Tests\Controller\MemberTestBase;

use LaDanse\SiteBundle\Tests\Controller\AccountConst;

class CalendarControllerTest extends MemberTestBase
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
            $crawler->filter('html:contains("Can\'t find the event you were looking for?")')->count() > 0,
            'Did not contain string "Can\'t find the event you were looking for?"'
        );

        $this->assertTrue(
            $crawler->filter('html:contains("sunday")')->count() > 0,
            'Did not contain string "sunday"'
        );
    }

    protected function getUrl(Client $client, $parameters = array())
    {
        return $this->generateUrl($client, "calendarIndex");
    }
}
