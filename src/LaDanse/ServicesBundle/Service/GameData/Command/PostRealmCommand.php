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
use LaDanse\ServicesBundle\Service\DTO\GameData\RealmMapper;
use LaDanse\ServicesBundle\Service\GameData\RealmAlreadyExistsException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(PostRealmCommand::SERVICE_NAME, public=true, shared=false)
 */
class PostRealmCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PostRealmCommand';

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

    /** @var DTO\GameData\PatchRealm $patchRealm */
    private $patchRealm;

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
     * @return DTO\GameData\PatchRealm
     */
    public function getPatchRealm(): DTO\GameData\PatchRealm
    {
        return $this->patchRealm;
    }

    /**
     * @param DTO\GameData\PatchRealm $patchRealm
     * @return PostRealmCommand
     */
    public function setPatchRealm(DTO\GameData\PatchRealm $patchRealm): PostRealmCommand
    {
        $this->patchRealm = $patchRealm;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->patchRealm == null || $this->patchRealm->getName() == null)
        {
            throw new InvalidInputException("Given PatchRealm was null or name of realm was null", 400);
        }
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('r')
            ->from('LaDanse\DomainBundle\Entity\GameData\Realm', 'r')
            ->where('r.name = ?1')
            ->setParameter(1, $this->getPatchRealm()->getName());

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving Realm by name ",
            [
                "query" => $qb->getDQL()
            ]
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $realms = $query->getResult();

        if (count($realms) != null)
        {
            throw new RealmAlreadyExistsException(
                "Realm with name '" . $this->getPatchRealm()->getName() . "' already exists", 400
            );
        }

        /* verify that the user is allowed to create a realm */
        /*
         * Disable until we have proper support for Commands.
         *
        if (!$this->authzService->evaluate(
            new SubjectReference($this->getAccount()),
            ActivityType::REALM_CREATE,
            new ResourceByValue(DTO\GameData\PatchRealm::class, null, $this->getPatchRealm())))
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to create a realm',
                [
                    "account" => $this->getAccount()->getId(),
                    "realm" => $this->getPatchRealm()->getName()
                ]
            );

            throw new NotAuthorizedException("Current user is not allowed to create a new realm", 401);
        }
         */

        $newRealm = new Entity\GameData\Realm();

        $newRealm->setName($this->getPatchRealm()->getName());

        $em->persist($newRealm);
        $em->flush();

        $dtoRealm = RealmMapper::mapSingle($newRealm);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::REALM_CREATE,
                $this->isAuthenticated() ? $this->getAccount() : null,
                [
                    'accountId'  => $this->isAuthenticated() ? $this->getAccount()->getId() : null,
                    'patchRealm' => ActivityEvent::annotatedToSimpleObject($this->getPatchRealm())
                ]
            )
        );

        return $dtoRealm;
    }
}