<?php

namespace LaDanse\ServicesBundle\Service\Character\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Character\CharacterSession;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(UntrackCharacterCommand::SERVICE_NAME, public=true, shared=false)
 */
class UntrackCharacterCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.UntrackCharacterCommand';

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

    /** @var CharacterSession */
    private $characterSession;

    /** @var int */
    private $characterId;

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
     *
     * @return UntrackCharacterCommand
     */
    public function setCharacterSession(CharacterSession $characterSession): UntrackCharacterCommand
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
     * @return UntrackCharacterCommand
     */
    public function setCharacterId(int $characterId): UntrackCharacterCommand
    {
        $this->characterId = $characterId;
        return $this;
    }

    protected function validateInput()
    {
        if (!($this->getCharacterSession() instanceof CharacterSessionImpl))
        {
            throw new InvalidInputException("Unrecognized CharacterSession implementation");
        }
    }

    protected function runCommand()
    {
        /*
         * Find an active tracker for characterSource
         *  if found
         *      end it
         *  if not found
         *      throw exception, to untrack a character it must first be tracked
         *
         * Verify if any active trackers are left
         *  if no trackers
         *      end character, end claims, end guild
         *  if trackers left
         *      do nothing
         */

        // create a shared $fromTime since we will need it often below
        $endTime = new \DateTime();

        /** @var CharacterSessionImpl $characterSessionImpl */
        $characterSessionImpl = $this->getCharacterSession();

        $em = $this->doctrine->getManager();

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
                    "The character %s is not being tracked by current characterSource, cannot untrack it",
                    $this->getCharacterId()
                ),
                400
            );
        }

        // close the TrackedBy held by this characterSource
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update(Entity\CharacterOrigin\TrackedBy::class, 'trackedBy')
            ->set('trackedBy.endTime', '?1')
            ->where($qb->expr()->eq('trackedBy.character', '?2'))
            ->setParameter(1, $endTime)
            ->setParameter(2, $em->getReference(Entity\Character::class, $this->getCharacterId()))
            ->getQuery()->execute();

        // search for any other active trackers
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('trackedBy')
            ->from(Entity\CharacterOrigin\TrackedBy::class, 'trackedBy')
            ->where('trackedBy.character = ?1')
            ->andWhere('trackedBy.fromTime IS NOT NULL')
            ->andWhere('trackedBy.endTime IS NULL')
            ->setParameter(
                1,
                $em->getReference(Entity\Character::class, $this->getCharacterId())
            );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $trackedBys = $query->getResult();

        if (count($trackedBys) == 0)
        {
            // nobody else is tracking this character, clean up

            // close the character
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            $qb->update(Entity\Character::class, 'char')
                ->set('char.endTime', '?1')
                ->where('char.id = ?2')
                ->setParameter(1, $endTime)
                ->setParameter(2, $this->getCharacterId())
                ->getQuery()->execute();

            // close all character versions (should only be one)
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            $qb->update(Entity\CharacterVersion::class, 'charVersion')
                ->set('charVersion.endTime', '?1')
                ->where('charVersion.character = ?2')
                ->setParameter(1, $endTime)
                ->setParameter(2, $em->getReference(Entity\Character::class, $this->getCharacterId()))
                ->getQuery()->execute();

            // close all claims
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            $qb->update(Entity\Claim::class, 'claim')
                ->set('claim.endTime', '?1')
                ->where('claim.character = ?2')
                ->setParameter(1, $endTime)
                ->setParameter(2, $em->getReference(Entity\Character::class, $this->getCharacterId()))
                ->getQuery()->execute();

            // close all PlayRoles associated with above claims

            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            /** @var \Doctrine\ORM\QueryBuilder $innerQb */
            $innerQb = $em->createQueryBuilder();

            $qb->update(Entity\PlaysRole::class, 'playsRole')
                ->set('playsRole.endTime', '?1')
                ->where(
                    $qb->expr()->in(
                        'playsRole.claim',
                        $innerQb->select('claim.id')
                            ->from(Entity\Claim::class, 'claim')
                            ->add('where',
                                $innerQb->expr()->eq('claim.character', '?2')
                            )->getDQL()
                    )
                )
                ->setParameter(1, $endTime)
                ->setParameter(2, $em->getReference(Entity\Character::class, $this->getCharacterId()))
                ->getQuery()->execute();

            // close InGuild it if exists
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            $qb->update(Entity\InGuild::class, 'inGuild')
                ->set('inGuild.endTime', '?1')
                ->where('inGuild.character = ?2')
                ->setParameter(1, $endTime)
                ->setParameter(2, $em->getReference(Entity\Character::class, $this->getCharacterId()))
                ->getQuery()->execute();
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_UNTRACK,
                $this->getAccount(),
                [
                    'accountId'      => $this->getAccount()->getId(),
                    'characterId'    => $this->getCharacterId()
                ]
            )
        );
    }
}