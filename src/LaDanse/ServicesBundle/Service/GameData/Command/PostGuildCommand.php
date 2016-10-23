<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GameData\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Service\DTO\GameData\GuildMapper;
use LaDanse\ServicesBundle\Service\GameData\GuildAlreadyExistsException;
use LaDanse\ServicesBundle\Service\GameData\RealmDoesNotExistException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(PostGuildCommand::SERVICE_NAME, public=true, shared=false)
 */
class PostGuildCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PostGuildCommand';

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

    /** @var DTO\GameData\PatchGuild $patchGuild */
    private $patchGuild;

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
     * @return DTO\GameData\PatchGuild
     */
    public function getPatchGuild(): DTO\GameData\PatchGuild
    {
        return $this->patchGuild;
    }

    /**
     * @param DTO\GameData\PatchGuild $patchGuild
     * @return PostGuildCommand
     */
    public function setPatchGuild(DTO\GameData\PatchGuild $patchGuild): PostGuildCommand
    {
        $this->patchGuild = $patchGuild;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->patchGuild == null
            || $this->patchGuild->getName() == null
            || $this->patchGuild->getRealmId() == null
        )
        {
            throw new InvalidInputException("Given GuildRealm was null or properties were null", 400);
        }
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('g', 'r')
            ->from(Entity\GameData\Guild::class, 'g')
            ->leftJoin('g.realm', 'r')
            ->where('g.name = ?1')
            ->andWhere('r.id = ?2')
            ->setParameter(1, $this->getPatchGuild()->getName())
            ->setParameter(2, $this->getPatchGuild()->getRealmId());

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving Guild by name and realm",
            [
                "query" => $qb->getDQL()
            ]
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $guilds = $query->getResult();

        if (count($guilds) != null)
        {
            throw new GuildAlreadyExistsException(
                "Guild with name '"
                . $this->getPatchGuild()->getName()
                . "' on realm '"
                . $this->getPatchGuild()->getRealmId()
                . "' already exists", 400
            );
        }

        /* verify that the user is allowed to create a guild */
        /*
         * Disable until we have proper support for Commands.
         *
        if (!$this->authzService->evaluate(
            new SubjectReference($this->getAccount()),
            ActivityType::REALM_CREATE,
            new ResourceByValue(DTO\GameData\PatchGuild::class, null, $this->getPatchGuild())))
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to create a guild',
                [
                    "account" => $this->getAccount()->getId()
                ]
            );

            throw new NotAuthorizedException("Current user is not allowed to create a new realm", 401);
        }
        */

        $realmRepo = $em->getRepository(Entity\GameData\Realm::REPOSITORY);

        $realm = $realmRepo->find($this->getPatchGuild()->getRealmId());

        if ($realm == null)
        {
            throw new RealmDoesNotExistException(
                "Realm with id '" . $this->getPatchGuild()->getRealmId() . "' does not exist",
                400
            );
        }

        $newGuild = new Entity\GameData\Guild();

        $newGuild->setName($this->getPatchGuild()->getName());
        $newGuild->setRealm($realm);

        $em->persist($newGuild);
        $em->flush();

        $dtoGuild = GuildMapper::mapSingle($newGuild);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::GUILD_CREATE,
                $this->isAuthenticated() ? $this->getAccount() : null,
                [
                    'accountId'  => $this->isAuthenticated() ? $this->getAccount()->getId() : null,
                    'patchGuild' => ActivityEvent::annotatedToSimpleObject($this->getPatchGuild())
                ]
            )
        );

        return $dtoGuild;
    }
}