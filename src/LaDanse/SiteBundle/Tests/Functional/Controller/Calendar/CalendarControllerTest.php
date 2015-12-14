<?php

namespace LaDanse\SiteBundle\Tests\Functional\Controller\Calendar;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class CalendarControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testMember()
    {
        $fixtures = $this->loadFixtureFiles(array(
            '@LaDanseSiteBundle/Tests/Fixtures/account.yml'
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

        $crawler = $client->request('GET', '/calendar/');

        $this->assertTrue(
            $client->getResponse()->isSuccessful(),
            'Request was not succesful'
        );

        $this->assertTrue(
            $crawler->filter('html:contains("Can\'t find the event you were looking for?")')->count() > 0,
            'Did not contain string "Can\'t find the event you were looking for?"'
        );

        $this->assertTrue(
            $crawler->filter('html:contains("sunday")')->count() > 0,
            'Did not contain string "sunday"'
        );
    }
}
