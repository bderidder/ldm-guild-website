<?php

namespace LaDanse\ServicesBundle\Service\Discord;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\Discord\Command\AuthCodeRequestCommand;
use LaDanse\ServicesBundle\Service\Discord\Command\DisconnectDiscordCommand;
use LaDanse\ServicesBundle\Service\Discord\Command\GrantAccessTokenCommand;
use LaDanse\ServicesBundle\Service\Discord\Query\GetDiscordConnectStatusQuery;
use LaDanse\ServicesBundle\Service\DTO\Discord\AccessTokenGrant;
use LaDanse\ServicesBundle\Service\DTO\Discord\DiscordConnectStatus;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DiscordConnectService
 * @package LaDanse\ServicesBundle\Service\Discord
 *
 * @DI\Service(DiscordConnectService::SERVICE_NAME, public=true)
 */
class DiscordConnectService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.DiscordConnectService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

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
     * @param int $forAccountId
     * @param string $nonce
     *
     * @return string
     */
    public function authCodeRequest(int $forAccountId, string $nonce)
    {
        /** @var AuthCodeRequestCommand $cmd */
        $cmd = $this->get(AuthCodeRequestCommand::SERVICE_NAME);

        $cmd->setForAccountId($forAccountId);
        $cmd->setNonce($nonce);

        return $cmd->run();
    }

    /**
     * @param string $authCode
     *
     * @return AccessTokenGrant
     */
    public function grantAccessTokenRequest(string $authCode)
    {
        /** @var GrantAccessTokenCommand $cmd */
        $cmd = $this->get(GrantAccessTokenCommand::SERVICE_NAME);

        $cmd->setAuthCode($authCode);

        return $cmd->run();
    }

    /**
     * @param int $forAccountId
     *
     * @return DiscordConnectStatus
     */
    public function getDiscordConnectStatus(int $forAccountId)
    {
        /** @var GetDiscordConnectStatusQuery $query */
        $query = $this->get(GetDiscordConnectStatusQuery::SERVICE_NAME);

        $query->setForAccountId($forAccountId);

        return $query->run();
    }

    public function disconnectDiscord(int $forAccountId)
    {
        /** @var DisconnectDiscordCommand $cmd */
        $cmd = $this->get(DisconnectDiscordCommand::SERVICE_NAME);

        $cmd->setForAccountId($forAccountId);

        return $cmd->run();
    }
}