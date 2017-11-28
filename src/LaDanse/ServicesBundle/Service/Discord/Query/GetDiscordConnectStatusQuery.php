<?php

namespace LaDanse\ServicesBundle\Service\Discord\Query;

use Doctrine\Common\Collections\Criteria;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\DomainBundle\Entity\Discord\DiscordAccessToken;
use LaDanse\DomainBundle\Entity\Discord\DiscordAuthCode;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\DTO\Discord\AccessTokenGrant;
use LaDanse\ServicesBundle\Service\DTO\Discord\DiscordConnectStatus;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(GetDiscordConnectStatusQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetDiscordConnectStatusQuery extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.GetDiscordConnectStatusQuery';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @var AuthorizationService $authzService
     * @DI\Inject(AuthorizationService::SERVICE_NAME)
     */
    public $authzService;

    /**
     * @var int
     */
    private $forAccountId;

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
     * @return int
     */
    public function getForAccountId(): int
    {
        return $this->forAccountId;
    }

    /**
     * @param int $forAccountId
     */
    public function setForAccountId(int $forAccountId)
    {
        $this->forAccountId = $forAccountId;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        /** @var Entity\Account $forAccount */
        $forAccount = $this->doctrine->getRepository(Entity\Account::REPOSITORY)->find($this->forAccountId);

        /* @var \Doctrine\ORM\EntityRepository $accessTokenRepo */
        $accessTokenRepo = $this->doctrine->getRepository(DiscordAccessToken::REPOSITORY);

        $discordAccessTokens = $accessTokenRepo->matching(
            Criteria::create()->where(
                Criteria::expr()->andX(
                    Criteria::expr()->eq("account", $forAccount),
                    Criteria::expr()->eq("state", DiscordAccessToken::STATE_ACTIVE)
                ))
        );

        $connected = count($discordAccessTokens) == 1;

        if ($connected)
        {
            $this->authzService->allowOrThrow(
                new SubjectReference($this->getAccount()),
                ActivityType::AUTHZ_DISCORD_CONNECT_STATUS,
                new ResourceByValue(DiscordAccessToken::class, $discordAccessTokens[0])
            );

            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::AUTHZ_DISCORD_CONNECT_STATUS,
                    $this->getAccount(),
                    null
                )
            );

            $connectStatus = new DiscordConnectStatus();

            $connectStatus->setConnected($connected);

            return $connectStatus;
        }
        else
        {
            $connectStatus = new DiscordConnectStatus();

            $connectStatus->setConnected($connected);

            return $connectStatus;
        }
    }
}