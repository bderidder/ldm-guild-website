<?php

namespace LaDanse\ServicesBundle\Service\Discord\Command;

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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(GrantAccessTokenCommand::SERVICE_NAME, public=true, shared=false)
 */
class GrantAccessTokenCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.GrantAccessTokenCommand';

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
     * @var string
     */
    private $authCode;

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
     * @return string
     */
    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     */
    public function setAuthCode(string $authCode)
    {
        $this->authCode = $authCode;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        /* @var \Doctrine\ORM\EntityRepository $authCodeRepo */
        $authCodeRepo = $this->doctrine->getRepository(DiscordAuthCode::REPOSITORY);

        /* @var \Doctrine\ORM\EntityRepository $accessTokenRepo */
        $accessTokenRepo = $this->doctrine->getRepository(DiscordAccessToken::REPOSITORY);

        $discordAuthCodes = $authCodeRepo->matching(
            Criteria::create()->where(Criteria::expr()->eq("authCode", $this->authCode)));

        if (count($discordAuthCodes) != 1)
        {
            throw new InvalidInputException("Invalid auth code");
        }

        /** @var DiscordAuthCode $discordAuthCode */
        $discordAuthCode = $discordAuthCodes[0];

        if ($discordAuthCode->getState() != DiscordAuthCode::STATE_PENDING)
        {
            throw new InvalidInputException("Invalid auth code");
        }

        if (!$this->isStillValid($discordAuthCode->getCreationDate()))
        {
            throw new InvalidInputException("authCode has expired");
        }

        // if we got here, we consider the authCode valid and we will grant an access token

        $discordAuthCode->setState(DiscordAuthCode::STATE_CONSUMED);

        // set any Active access token for this user to Removed as we will issue a new one
        $discordAccessTokens = $accessTokenRepo->matching(
            Criteria::create()->where(Criteria::expr()->eq("account", $discordAuthCode->getAccount())));

        foreach($discordAccessTokens as $discordAccessToken)
        {
            /** @var DiscordAccessToken $discordAccessToken */

            if ($discordAccessToken->getState() == DiscordAccessToken::STATE_ACTIVE)
            {
                $discordAccessToken->setState(DiscordAccessToken::STATE_REMOVED);
            }
        }

        $discordAccessToken = new DiscordAccessToken();

        $accessToken = bin2hex(random_bytes(32));

        $discordAccessToken->setState(DiscordAccessToken::STATE_ACTIVE);
        $discordAccessToken->setAccessToken($accessToken);
        $discordAccessToken->setCreationDate(time());
        $discordAccessToken->setAccount($discordAuthCode->getAccount());

        $this->doctrine->getManager()->persist($discordAccessToken);

        $this->doctrine->getManager()->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::AUTHZ_DISCORD_GRANT_TOKEN,
                $this->getAccount(),
                null
            )
        );

        $accessTokenGrant = new AccessTokenGrant();

        $accessTokenGrant
            ->setNonce($discordAuthCode->getNonce())
            ->setAccessToken($accessToken)
            ->setIssuedOn($discordAccessToken->getCreationDate());

        return $accessTokenGrant;
    }

    private function isStillValid(int $timestamp)
    {
        $currentTimestamp = time();

        $allowedDeviation = 5 * 60; // number of seconds we allow the auth code to be used

        return ($timestamp + $allowedDeviation) > $currentTimestamp;
    }
}