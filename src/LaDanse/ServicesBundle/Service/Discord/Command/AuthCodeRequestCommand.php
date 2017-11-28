<?php

namespace LaDanse\ServicesBundle\Service\Discord\Command;

use Doctrine\Common\Collections\Criteria;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\DomainBundle\Entity\Discord\DiscordAuthCode;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(AuthCodeRequestCommand::SERVICE_NAME, public=true, shared=false)
 */
class AuthCodeRequestCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.AuthCodeRequestCommand';

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
     * @var string
     */
    private $nonce;

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

    /**
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param string $nonce
     */
    public function setNonce(string $nonce)
    {
        $this->nonce = $nonce;
    }

    protected function validateInput()
    {
        if (strlen(trim($this->nonce)) < 1)
        {
            throw new InvalidInputException("Nonce cannot be empty");
        }
    }

    protected function runCommand()
    {
        $this->authzService->allowOrThrow(
            new SubjectReference($this->getAccount()),
            ActivityType::AUTHZ_DISCORD_REQUEST_AUTHCODE,
            new ResourceByValue("int", $this->forAccountId)
        );

        /** @var Entity\Account $forAccount */
        $forAccount = $this->doctrine->getRepository(Entity\Account::REPOSITORY)->find($this->forAccountId);

        if ($forAccount == null)
        {
            throw new InvalidInputException("For account does not exist");
        }

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository(DiscordAuthCode::REPOSITORY);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->eq("account", $forAccount),
                Criteria::expr()->eq("state", DiscordAuthCode::STATE_PENDING)
            )
            );

        $discordAuthCodes = $repository->matching($criteria);

        foreach($discordAuthCodes as $discordAuthCode)
        {
            /** @var DiscordAuthCode $discordAuthCode */
            $discordAuthCode->setState(DiscordAuthCode::STATE_REMOVED);
        }

        $discordAuthCode = new DiscordAuthCode();

        $authCode = bin2hex(random_bytes(16));

        $discordAuthCode->setState(DiscordAuthCode::STATE_PENDING);
        $discordAuthCode->setAccount($forAccount);
        $discordAuthCode->setNonce($this->nonce);
        $discordAuthCode->setAuthCode($authCode);
        $discordAuthCode->setCreationDate(time());

        $this->doctrine->getManager()->persist($discordAuthCode);

        $this->doctrine->getManager()->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::AUTHZ_DISCORD_REQUEST_AUTHCODE,
                $this->getAccount(),
                null
            )
        );

        return $authCode;
    }
}