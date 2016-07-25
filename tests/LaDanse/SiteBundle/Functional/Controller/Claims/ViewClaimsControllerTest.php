<?php

namespace Tests\LaDanse\SiteBundle\Functional\Controller\Claims;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group FunctionalTest
 */
class ViewClaimsControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testMember()
    {
        $fixtures = $this->loadFixtureFiles(array(
            'tests/LaDanse/SiteBundle/Fixtures/account.yml'
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

        $crawler = $client->request('GET', '/claims/');

        $this->assertTrue(
            $crawler->filter('html:contains("Below you can find the list of")')->count() == 1
        );

        $this->assertTrue(
            $crawler->filter('html:contains("add a character")')->count() == 1
        );
    }
}
