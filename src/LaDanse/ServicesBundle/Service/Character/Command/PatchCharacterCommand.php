<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\InGuild;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use LaDanse\ServicesBundle\Service\Character\CharacterSession;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(PatchCharacterCommand::SERVICE_NAME, public=true, shared=false)
 */
class PatchCharacterCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PatchCharacterCommand';

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

    /** @var int */
    private $characterId;

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
     * @return PatchCharacterCommand
     */
    public function setCharacterSession(CharacterSession $characterSession): PatchCharacterCommand
    {
        $this->characterSession = $characterSession;
        return $this;
    }

    /**
     * @return int
     */
    public function getCharacterId(): int
    {
        return $this->characterId;
    }

    /**
     * @param int $characterId
     * @return PatchCharacterCommand
     */
    public function setCharacterId(int $characterId): PatchCharacterCommand
    {
        $this->characterId = $characterId;
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
     * @return PatchCharacterCommand
     */
    public function setPatchCharacter(DTO\Character\PatchCharacter $patchCharacter): PatchCharacterCommand
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
        // create a shared $fromTime since we might need it often below
        $fromTime = new \DateTime();

        $em = $this->doctrine->getManager();

        /** @var CharacterSessionImpl $characterSessionImpl */
        $characterSessionImpl = $this->getCharacterSession();

        // verify if the characterSource actually tracks this character

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('trackedBy')
            ->from(Entity\CharacterOrigin\TrackedBy::class, 'trackedBy')
            ->join(Entity\CharacterOrigin\CharacterSource::class, 'characterSource')
            ->where('trackedBy.character = ?1')
            ->andWhere('trackedBy.characterSource = ?2')
            ->andWhere('trackedBy.fromTime IS NOT NULL')
            ->andWhere('trackedBy.endTime IS NULL')
            ->setParameter(
                1,
                $em->getReference(Entity\Character::class, $this->getCharacterId())
            )
            ->setParameter(2, $characterSessionImpl->getCharacterSource());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $trackedBys = $query->getResult();

        if (count($trackedBys) == 0)
        {
            throw new ServiceException(
                sprintf(
                    "The character %s is not being tracked by current characterSource, use POST to create it",
                    $this->getCharacterId()
                ),
                400
            );
        }

        /**
         * check if character already exists (name + realm as combined unique key)
         *  if it already exists
         *      verify if the characterSource isn't already tracking this character
         *          if it is already being tracked, throw exception as it should do a PUT and not a POST
         *          if it is not being tracked, add tracker
         *      define a delta and update character
         *  if it does not exist
         *      create character and add tracker
         */

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('charVersion', 'char', 'gameRace', 'gameClass', 'realm')
            ->from(Entity\CharacterVersion::class, 'charVersion')
            ->join('charVersion.character', 'char')
            ->join('charVersion.gameRace', 'gameRace')
            ->join('charVersion.gameClass', 'gameClass')
            ->join('char.realm', 'realm')
            ->where('char.id = ?1')
            ->andWhere('char.fromTime IS NOT NULL')
            ->andWhere('char.endTime IS NULL')
            ->andWhere('charVersion.fromTime IS NOT NULL')
            ->andWhere('charVersion.endTime IS NULL')
            ->setParameter(1, $this->getCharacterId());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $characterVersions = $query->getResult();

        if (count($characterVersions) == 0)
        {
            // character does not yet exist, should never happen

            throw new ServiceException(
                sprintf(
                    "An active character with id %s was not found, use POST to create it",
                    $this->getCharacterId()
                ),
                400
            );
        }
        elseif(count($characterVersions) != 1)
        {
            // this should never happen, we cannot have two characters with the same name on the same realm

            throw new ServiceException(
                sprintf(
                    "Multiple characters with the id %s found",
                    $this->getCharacterId()
                ),
                500
            );
        }

        /** @var Entity\CharacterVersion $currentCharacterVersion */
        $currentCharacterVersion = $characterVersions[0];

        /*
         * Verify that there is no attempt to change immutable fields
         */

        if ($currentCharacterVersion->getCharacter()->getName() != $this->getPatchCharacter()->getName()
            ||
            $currentCharacterVersion->getCharacter()->getRealm()->getId()
                != $this->getPatchCharacter()->getRealmReference()->getId())
        {
            throw new ServiceException(
                sprintf(
                    "Attempt to change name or realm on character with id %s",
                    $this->getCharacterId()
                ),
                400
            );
        }

        /*
         * Fields to compare:
         *
         * - in CharacterVersion
         *      - level
         *      - gameClass
         *      - gameRace
         * - in Character
         *      - Guild
         */

        // check if we need to make a new CharacterVersion

        if ($currentCharacterVersion->getLevel() != $this->getPatchCharacter()->getLevel()
            ||
            $currentCharacterVersion->getGameRace()->getId() != $this->getPatchCharacter()->getGameRaceReference()->getId()
            ||
            $currentCharacterVersion->getGameClass()->getId() != $this->getPatchCharacter()->getGameClassReference()->getId())
        {
            $currentCharacterVersion->setEndTime($fromTime);

            $newCharacterVersion = new Entity\CharacterVersion();

            $newCharacterVersion->setCharacter($currentCharacterVersion->getCharacter());
            $newCharacterVersion->setLevel($this->getPatchCharacter()->getLevel());
            $newCharacterVersion->setFromTime($fromTime);
            $newCharacterVersion->setGameRace(
                $em->getReference(
                    Entity\GameData\GameRace::class,
                    $this->getPatchCharacter()->getGameRaceReference()->getId()
                )
            );
            $newCharacterVersion->setGameClass(
                $em->getReference(
                    Entity\GameData\GameClass::class,
                    $this->getPatchCharacter()->getGameClassReference()->getId()
                )
            );

            $em->persist($newCharacterVersion);
            $em->flush();
        }

        // check if we need to change the guild association

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('inGuild', 'guild')
            ->from(Entity\InGuild::class, 'inGuild')
            ->join('inGuild.guild', 'guild')
            ->where('inGuild.character = ?1')
            ->andWhere('inGuild.fromTime IS NOT NULL')
            ->andWhere('inGuild.endTime IS NULL')
            ->setParameter(1, $currentCharacterVersion->getCharacter());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $inGuilds = $query->getResult();

        if (count($inGuilds) == 0)
        {
            // the character is currently not in a guild, verify if it should be

            if ($this->getPatchCharacter()->getGuildReference() != null)
            {
                $newInGuild = new InGuild();
                $newInGuild->setFromTime($fromTime);
                $newInGuild->setCharacter($currentCharacterVersion->getCharacter());
                $newInGuild->setGuild(
                    $em->getReference(
                        Entity\GameData\Guild::class,
                        $this->getPatchCharacter()->getGuildReference()->getId()
                    )
                );

                $em->persist($newInGuild);
                $em->flush();
            }
        }
        elseif (count($inGuilds) == 1)
        {
            // the character is currently in a guild, verify if it needs to change

            /** @var InGuild $inGuild */
            $inGuild = $inGuilds[0];

            if ($this->getPatchCharacter()->getGuildReference() == null)
            {
                $inGuild->setEndTime($fromTime);
                $em->flush();
            }
            else if ($inGuild->getGuild()->getId() != $this->getPatchCharacter()->getGuildReference()->getId())
            {
                $inGuild->setEndTime($fromTime);

                $newInGuild = new InGuild();
                $newInGuild->setFromTime($fromTime);
                $newInGuild->setCharacter($currentCharacterVersion->getCharacter());
                $newInGuild->setGuild(
                    $em->getReference(
                        Entity\GameData\Guild::class,
                        $this->getPatchCharacter()->getGuildReference()->getId()
                    )
                );

                $em->persist($newInGuild);
                $em->flush();
            }
        }
        else
        {
            // according to the database the character is in two guilds at the same time, this is an error

            throw new ServiceException(
                sprintf(
                    "Two active inGuilds for character %s ",
                    $this->getCharacterId()
                ),
                500
            );
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_UPDATE,
                $this->isAuthenticated() ? $this->getAccount() : null,
                [
                    'accountId'      => $this->isAuthenticated() ? $this->getAccount()->getId() : null,
                    'characterId'    => $this->getCharacterId(),
                    'patchCharacter' => ActivityEvent::annotatedToSimpleObject($this->getPatchCharacter())
                ]
            )
        );

        /** @var CharacterService $characterService */
        $characterService = $this->container->get(CharacterService::SERVICE_NAME);

        return $characterService->getCharacterById($this->getCharacterId());
    }
}