<?php

namespace LaDanse\ServicesBundle\Service\SocialConnect;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Service\SocialConnect\Command\DisconnectAccountCommand;
use LaDanse\ServicesBundle\Service\SocialConnect\Query\GetAccessTokenForAccountQuery;
use LaDanse\ServicesBundle\Service\SocialConnect\Query\IsAccountConnectedQuery;
use LaDanse\ServicesBundle\Service\SocialConnect\Query\VerifyAccountConnectionQuery;
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
        /** @var IsAccountConnectedQuery $isAccountConnectedQuery */
        $isAccountConnectedQuery = $this->get(IsAccountConnectedQuery::SERVICE_NAME);

        $isAccountConnectedQuery->setAccount($account);

        return $isAccountConnectedQuery->run();
    }

    /**
     * @param Account $account
     *
     * @return string
     */
    public function getAccessTokenForAccount(Account $account)
    {
        /** @var GetAccessTokenForAccountQuery $getAccessTokenForAccountQuery */
        $getAccessTokenForAccountQuery = $this->get(GetAccessTokenForAccountQuery::SERVICE_NAME);

        $getAccessTokenForAccountQuery->setAccount($account);

        return $getAccessTokenForAccountQuery->run();
    }

    /**
     * @param Account $account
     *
     * @return VerificationReport
     */
    public function verifyAccountConnection(Account $account)
    {
        /** @var VerifyAccountConnectionQuery $verifyAccountConnectionCommand */
        $verifyAccountConnectionCommand = $this->get(VerifyAccountConnectionQuery::SERVICE_NAME);

        $verifyAccountConnectionCommand->setAccount($account);

        return $verifyAccountConnectionCommand->run();
    }

    /**
     * @param Account $account
     */
    public function disconnectAccount(Account $account)
    {
        /** @var DisconnectAccountCommand $disconnectAccountCommand */
        $disconnectAccountCommand = $this->get(DisconnectAccountCommand::SERVICE_NAME);

        $disconnectAccountCommand->setAccount($account);

        $disconnectAccountCommand->run();
    }
}