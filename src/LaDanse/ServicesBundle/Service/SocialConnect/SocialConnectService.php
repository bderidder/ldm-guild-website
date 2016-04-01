<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\SocialConnect;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(SocialConnectService::SERVICE_NAME, public=true)
 */
class SocialConnectService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.SocialConnectService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

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
     *
     * @return bool
     */
    public function isAccountConnected(Account $account)
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $account));

        return count($socialConnects) == 1;
    }

    /**
     * @param Account $account
     */
    public function disconnectAccount(Account $account)
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $account));

        if (count($socialConnects) == 1)
        {
            $this->doctrine->getManager()->remove($socialConnects[0]);
            $this->doctrine->getManager()->flush();
        }
    }

    /**
     * @param Account $account
     *
     * @return string
     */
    public function getAccessTokenForAccount(Account $account)
    {
        $repo = $this->doctrine->getRepository(SocialConnect::REPOSITORY);

        $socialConnects = $repo->findBy(array('account' => $account));

        if (count($socialConnects) == 1)
        {
            return $socialConnects[0]->getAccessToken();
        }

        return null;
    }

    /**
     * @param Account $account
     *
     * @return VerificationReport
     */
    public function verifyAccountConnection(Account $account)
    {
        /** @var SocialConnectService $socialConnectService */
        $socialConnectService = $this->get(SocialConnectService::SERVICE_NAME);

        $checkTokenUrl = 'https://eu.battle.net/oauth/check_token';
        $charactersUrl = 'https://eu.api.battle.net/wow/user/characters';

        $accessToken = $socialConnectService->getAccessTokenForAccount($account);

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

                $response = $client->get($checkTokenUrl, array(
                    'query' => array('token' => $accessToken)
                ));

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

                $response = $client->get($charactersUrl, array(
                    'query' => array('access_token' => $accessToken)
                ));

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