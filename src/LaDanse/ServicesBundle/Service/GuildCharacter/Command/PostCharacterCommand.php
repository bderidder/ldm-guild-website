<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Command;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\GuildCharacter\CharacterService;
use LaDanse\ServicesBundle\Service\GuildCharacter\CharacterSession;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(PostCharacterCommand::SERVICE_NAME, public=true, shared=false)
 */
class PostCharacterCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PostCharacterCommand';

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

    /** @var CharacterSession */
    private $characterSession;

    /** @var DTO\Character\PatchCharacter */
    private $patchCharacter;

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
     * @return CharacterSession
     */
    public function getCharacterSession(): CharacterSession
    {
        return $this->characterSession;
    }

    /**
     * @param CharacterSession $characterSession
     * @return PostCharacterCommand
     */
    public function setCharacterSession(CharacterSession $characterSession): PostCharacterCommand
    {
        $this->characterSession = $characterSession;
        return $this;
    }

    /**
     * @return DTO\Character\PatchCharacter
     */
    public function getPatchCharacter(): DTO\Character\PatchCharacter
    {
        return $this->patchCharacter;
    }

    /**
     * @param DTO\Character\PatchCharacter $patchCharacter
     * @return PostCharacterCommand
     */
    public function setPatchCharacter(DTO\Character\PatchCharacter $patchCharacter): PostCharacterCommand
    {
        $this->patchCharacter = $patchCharacter;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->getPatchCharacter() == null || $this->getCharacterSession() == null)
        {
            throw new InvalidInputException("characterSession or patchCharacter can't be null");
        }

        if (!($this->getCharacterSession() instanceof CharacterSessionImpl))
        {
            throw new InvalidInputException("Unrecognized CharacterSession implementation");
        }
    }

    protected function runCommand()
    {
        /** @var CharacterSessionImpl $characterSessionImpl */
        $characterSessionImpl = $this->getCharacterSession();

        /**
         * check if character already exists (name + realm as combined unique key)
         *  if it already exists
         *      verify if the characterSource isn't already tracking this character
         *          if it is already being tracked, throw exception as it should do a PUT and not a POST
         *          if it is not being tracked, add tracker and update
         *  if it does not exist
         *      create character and add tracker
         */

        // create a shared $fromTime since we will need it often below
        $fromTime = new \DateTime();

        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('c')
            ->from(Entity\Character::class, 'c')
            ->join('c.realm', 'realm')
            ->where('c.name = ?1')
            ->andWhere('realm.id = ?2')
            ->andWhere('c.fromTime IS NOT NULL')
            ->andWhere('c.endTime IS NULL')
            ->setParameter(1, $this->getPatchCharacter()->getName())
            ->setParameter(
                2,
                $em->getReference(Entity\GameData\Realm::class, $this->getPatchCharacter()->getRealmReference()->getId()                )
            );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $characters = $query->getResult();

        if (count($characters) == 0)
        {
            // character does not yet exist, so we have to create it and create a tracker for the character source

            try {
                $character = new Entity\Character();
                $character->setName($this->getPatchCharacter()->getName());
                $character->setFromTime($fromTime);
                $character->setRealm(
                    $em->getReference(
                        Entity\GameData\Realm::class,
                        $this->getPatchCharacter()->getRealmReference()->getId()
                    )
                );

                $em->persist($character);

                $characterVersion = new Entity\CharacterVersion();
                $characterVersion->setCharacter($character);
                $characterVersion->setLevel($this->getPatchCharacter()->getLevel());
                $characterVersion->setFromTime($fromTime);
                $characterVersion->setGameClass(
                    $em->getReference(
                        Entity\GameData\GameClass::class,
                        $this->getPatchCharacter()->getGameClassReference()->getId()
                    )
                );
                $characterVersion->setGameRace(
                    $em->getReference(
                        Entity\GameData\GameRace::class,
                        $this->getPatchCharacter()->getGameRaceReference()->getId()
                    )
                );

                $em->persist($characterVersion);

                $trackedBy = new Entity\CharacterOrigin\TrackedBy();
                $trackedBy->setCharacter($character);
                $trackedBy->setFromTime($fromTime);
                $trackedBy->setCharacterSource($characterSessionImpl->getCharacterSource());

                $em->persist($trackedBy);

                if ($this->getPatchCharacter()->getGuildReference() != null)
                {
                    $inGuild = new Entity\InGuild();
                    $inGuild->setCharacter($character);
                    $inGuild->setFromTime($fromTime);
                    $inGuild->setGuild(
                        $em->getReference(
                            Entity\GameData\Guild::class,
                            $this->getPatchCharacter()->getGuildReference()->getId()
                        )
                    );

                    $em->persist($inGuild);
                }

                $em->flush();

                /** @var CharacterService $characterService */
                $characterService = $this->container->get(CharacterService::SERVICE_NAME);

                return $characterService->getCharacterById($character->getId());
            }
            catch(ForeignKeyConstraintViolationException $exception)
            {
                throw new ServiceException(
                    sprintf(
                        "One or more of the given references did not point to an existing instance " .
                        "[gameClass %s, gameRace %s, realm %s]",
                        $this->getPatchCharacter()->getGameClassReference()->getId(),
                        $this->getPatchCharacter()->getGameRaceReference()->getId(),
                        $this->getPatchCharacter()->getRealmReference()->getId()
                    ),
                    400,
                    $exception
                );
            }
        }
        elseif(count($characters) == 1)
        {
            // character already exists, verify that this character source is not already tracking it

            /** @var Entity\Character $character */
            $character = $characters[0];

            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            $qb->select('t')
                ->from(Entity\CharacterOrigin\TrackedBy::class, 't')
                ->where('t.character = ?1')
                ->andWhere('t.characterSource = ?2')
                ->andWhere('t.fromTime IS NOT NULL')
                ->andWhere('t.endTime IS NULL')
                ->setParameter(1, $character)
                ->setParameter(2, $characterSessionImpl->getCharacterSource());

            /* @var $query \Doctrine\ORM\Query */
            $query = $qb->getQuery();

            $trackers = $query->getResult();

            if (count($trackers) != 0)
            {
                throw new ServiceException(
                    sprintf(
                        "The character %s is already actively tracked by characterSource %s",
                        $character->getId(),
                        $characterSessionImpl->getCharacterSource()->getId()
                    ),
                    400
                );
            }

            $trackedBy = new Entity\CharacterOrigin\TrackedBy();
            $trackedBy->setCharacter($character);
            $trackedBy->setFromTime($fromTime);
            $trackedBy->setCharacterSource($characterSessionImpl->getCharacterSource());

            $em->persist($trackedBy);

            /** @var CharacterService $characterService */
            $characterService = $this->container->get(CharacterService::SERVICE_NAME);

            return $characterService->patchCharacter(
                $this->getCharacterSession(),
                $character->getId(),
                $this->getPatchCharacter()
            );
        }
        else
        {
            // this should never happen, we cannot have two active characters with the same name on the same realm

            throw new ServiceException(
                sprintf(
                    "Two active characters with the name %s on the realm %s found",
                    $this->getPatchCharacter()->getName(),
                    $this->getPatchCharacter()->getRealmReference()->getId()
                ),
                400
            );
        }
    }
}