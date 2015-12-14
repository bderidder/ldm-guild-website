<?php

namespace LaDanse\SiteBundle\Tests\Functional\Controller\Menu;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class MenuControllerTest extends WebTestCase
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

        $client->followRedirects(true);
        $client->setMaxRedirects(5);

        $crawler = $client->request('GET', '/menu/');

        $this->assertTrue(
            $crawler->filter('html:contains("My WoW Characters")')->count() > 0
        );

        $this->assertTrue(
            $crawler->filter('html:contains("Pictures")')->count() > 0
        );
    }
}
