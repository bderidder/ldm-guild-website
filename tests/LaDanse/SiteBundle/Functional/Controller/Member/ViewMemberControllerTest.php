<?php

namespace Tests\LaDanse\SiteBundle\Functional\Controller\Member;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group FunctionalTest
 */
class ViewMemberControllerTest extends WebTestCase
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

        $crawler = $client->request('GET', '/roster/' . $account->getId());

        $this->assertTrue(
            $crawler->filter('html:contains("Below you can find the list of characters")')->count() == 1
        );
    }
}
