<?php

namespace LaDanse\SiteBundle\Tests\Functional\Controller\TeamSpeak;

use LaDanse\DomainBundle\Entity\Account;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class TeamSpeakControllerTest extends WebTestCase
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

        $crawler = $client->request('GET', '/teamspeak/');

        $this->assertTrue(
            $crawler->filter('html:contains("(click here to download client)")')->count() > 0,
            'The link "(click here to download client)" is not present'
        );
    }
}
