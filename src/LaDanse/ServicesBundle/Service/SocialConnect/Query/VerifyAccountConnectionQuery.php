<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect\Query;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Service\SocialConnect\VerificationReport;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(VerifyAccountConnectionQuery::SERVICE_NAME, public=true, shared=false)
 */
class VerifyAccountConnectionQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.VerifyAccountConnectionQuery';
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var Account $account */
    private $account;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @param Account $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    protected function validateInput()
    {
        return ($this->account != null);
    }

    protected function runQuery()
    {
        $checkTokenUrl = 'https://eu.battle.net/oauth/check_token';
        $charactersUrl = 'https://eu.api.battle.net/wow/user/characters';

        /** @var GetAccessTokenForAccountQuery $getAccessTokenForAccountQuery */
        $getAccessTokenForAccountQuery = $this->container->get(GetAccessTokenForAccountQuery::SERVICE_NAME);

        $getAccessTokenForAccountQuery->setAccount($this->account);

        $accessToken = $getAccessTokenForAccountQuery->run();

        $verificationReport = new VerificationReport();

        if ($accessToken == null)
        {
            $verificationReport->setConnected(false);
        }
        else
        {
            $verificationReport->setConnected(true);

            try
            {
                $client = new Client();

                $response = $client->get(
                    $checkTokenUrl,
                    [
                        'query' => ['token' => $accessToken],
                        'debug' => true,
                        'connect_timeout' => 10
                    ]
                );

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction check_token success " . $response->getBody());

                $jsonBody = $response->getBody();

                $checkAccessToken = json_decode($jsonBody);

                if (!property_exists($checkAccessToken, 'exp'))
                {
                    $verificationReport->setCheckAccessToken(false);
                }
                else
                {
                    $verificationReport->setCheckAccessToken(true);

                    $expirationDate = new \DateTime();
                    $expirationDate->setTimestamp($checkAccessToken->exp);

                    $verificationReport->setExpirationDate($expirationDate);
                }
            }
            catch(ClientException $e)
            {
                $verificationReport->setCheckAccessToken(false);

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction check_token failure " . $e->getMessage());
            }

            try
            {
                $client = new Client();

                $response = $client->get(
                    $charactersUrl,
                    [
                        'query' => ['access_token' => $accessToken],
                        'debug' => true,
                        'connect_timeout' => 10
                    ]
                );

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction characters success " . $response->getBody());

                $jsonBody = $response->getBody();

                $checkAccessToken = json_decode($jsonBody);

                if (!property_exists($checkAccessToken, 'characters'))
                {
                    $verificationReport->setCharactersLoaded(false);
                }
                else
                {
                    $verificationReport->setCharactersLoaded(true);
                }
            }
            catch(ClientException $e)
            {
                $verificationReport->setCharactersLoaded(false);

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction characters failure " . $e->getMessage());
            }
        }

        return $verificationReport;
    }
}