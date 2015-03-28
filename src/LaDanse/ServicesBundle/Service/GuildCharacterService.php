<?php

namespace LaDanse\ServicesBundle\Service;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Character;
use LaDanse\DomainBundle\Entity\CharacterVersion;
use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\PlaysRole;
use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(GuildCharacterService::SERVICE_NAME, public=true)
 */
class GuildCharacterService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.GuildCharacterService';

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

    public function getAllGuildCharacters(\DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectAllGuildCharacters.sql.twig')
        );
        $query->setParameter('onDateTime', $onDateTime);
        
        $characters = $query->getResult();

        $charModels = array();

        foreach($characters as $character)
        {
            $charModels[] = $this->characterToDto($character, $onDateTime);
        }

        return $charModels;
    }

    public function getGuildCharacter($characterId, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectGuildCharacter.sql.twig')
        );
        $query->setParameter('characterName', $characterId);
        $query->setParameter('onDateTime', $onDateTime);
        
        $characters = $query->getResult();

        if (count($characters) == 0)
        {
            throw new \Exception('No character could be found at that time.');
        }

        $character = $characters[0];

        return $this->characterToDto($character, $onDateTime);
    }

    public function getClaims($accountId, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectClaimsForAccount.sql.twig')
        );
        $query->setParameter('accountId', $accountId);
        $query->setParameter('onDateTime', $onDateTime);
        
        $claims = $query->getResult();

        $claimsModels = array();

        foreach($claims as $claim)
        {
            $claimsModels[] = $this->claimToDto($claim, $onDateTime);
        }

        return $claimsModels;
    }

    public function getClaimsForCharacter($characterName, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectClaimsForCharacter.sql.twig')
        );
        $query->setParameter('characterName', $characterName);
        $query->setParameter('onDateTime', $onDateTime);
        
        $claims = $query->getResult();

        $claimsModels = array();

        foreach($claims as $claim)
        {
            $claimsModels[] = $this->claimToDto($claim, $onDateTime);
        }

        return $claimsModels;
    }

    public function getClaim($claimId, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectActiveClaim.sql.twig')
        );
        $query->setParameter('claimId', $claimId);
        
        $claims = $query->getResult();

        if (count($claims) == 0)
        {
            return null;
        }

        $claim = $claims[0];
        
        $claimsModel = $this->claimToDto($claim, $onDateTime);
       
        return $claimsModel;
    }

    public function getAllCharacters(\DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectCharactersForAccount.sql.twig')
        );

        $query->setParameter('onDateTime', $onDateTime);

        $characters = $query->getResult();

        return $this->charactersToDtoArray($characters, $onDateTime);
    }

    public function getUnclaimedCharacters(\DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectUnclaimedCharacters.sql.twig')
        );
        $query->setParameter('onDateTime', $onDateTime);

        $characters = $query->getResult();

        return $this->charactersToDtoArray($characters, $onDateTime);
    }

    public function endCharacter($characterId)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Character::REPOSITORY);

        $character = $repo->find($characterId);

        $character->setEndTime(new \DateTime());

        $em->flush();

        $this->endClaimsForCharacter($character);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_REMOVE,
                null,
                array(
                    'character'   => $character->getName()
                )
            )
        );
    }

    public function getActiveClaimsForAccount($account, \DateTime $onDateTime = null)
    {

    }

    public function getActiveClaimsForCharacter($character, \DateTime $onDateTime = null)
    {

    }

    public function createClaim($accountId, $characterId, $playsTank, $playsHealer, $playsDPS)
    {
        $onDateTime = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $characterRepo \Doctrine\ORM\EntityRepository */
        $characterRepo = $em->getRepository(Character::REPOSITORY);
        /* @var $character \LaDanse\DomainBundle\Entity\Character */
        $character = $characterRepo->find($characterId);

        /* @var $accountRepo \Doctrine\ORM\EntityRepository */
        $accountRepo = $em->getRepository(Account::REPOSITORY);
        /* @var $account \LaDanse\DomainBundle\Entity\Account */
        $account = $accountRepo->find($accountId);

        $claim = new Claim();
        $claim->setCharacter($character)
              ->setAccount($account)
              ->setFromTime($onDateTime);

        if ($playsTank)
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, Role::TANK));
        }
        
        if ($playsHealer)
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, Role::HEALER));
        }

        if ($playsDPS)
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, Role::DPS));
        }

        $this->logger->info(__CLASS__ . ' persisting new claim');

        $em->persist($claim);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_CREATE,
                $account,
                array(
                    'character'   => $character->getName(),
                    'playsTank'   => $playsTank,
                    'playsHealer' => $playsHealer,
                    'playsDPS'    => $playsDPS
                ))
        );
    }

    public function updateClaim($claimId, $playsTank, $playsHealer, $playsDPS)
    {
        $onDateTime = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $claimRepo \Doctrine\ORM\EntityRepository */
        $claimRepo = $em->getRepository(Claim::REPOSITORY);
        /* @var $claim \LaDanse\DomainBundle\Entity\Claim */
        $claim = $claimRepo->find($claimId);

        $notCurrentPlaysTank = true;
        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($claim->getRoles() as $playsRole)
        {
            if ($playsRole->getRole() == Role::TANK
                and is_null($playsRole->getEndTime()))
            {
                $notCurrentPlaysTank = false;

                if (!$playsTank)
                {
                    $playsRole->setEndTime($onDateTime);

                    $this->logger->info(__CLASS__ . ' removed TANK role from claim ' . $claimId);
                }
            }
        }

        if ($notCurrentPlaysTank && $playsTank)
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, Role::TANK));
        }

        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($claim->getRoles() as $playsRole)
        {
            if ($playsRole->isRole(Role::HEALER) && !$playsHealer)
            {
                $playsRole->setEndTime($onDateTime);

                $this->logger->info(__CLASS__ . ' removed HEALER role from claim ' . $claimId);
            }

            if (!$playsRole->isRole(Role::HEALER) && $playsHealer)
            {
                $em->persist($this->createPlaysRole($onDateTime, $claim, Role::HEALER));

                $this->logger->info(__CLASS__ . ' added HEALER role to claim ' . $claimId);
            }

            if ($playsRole->isRole(Role::DPS) && !$playsDPS)
            {
                $playsRole->setEndTime($onDateTime);

                $this->logger->info(__CLASS__ . ' removed DPS role from claim ' . $claimId);
            }

            if (!$playsRole->isRole(Role::DPS) && $playsDPS)
            {
                $em->persist($this->createPlaysRole($onDateTime, $claim, Role::DPS));

                $this->logger->info(__CLASS__ . ' added DPS role to claim ' . $claimId);
            }
        }

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_EDIT,
                $claim->getAccount(),
                array(
                    'character'   => $claim->getCharacter()->getName(),
                    'playsTank'   => $playsTank,
                    'playsHealer' => $playsHealer,
                    'playsDPS'    => $playsDPS
                ))
        );
    }

    public function endClaim($claimId)
    {
        $onDateTime = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(Claim::REPOSITORY);
        /* @var $claim \LaDanse\DomainBundle\Entity\Claim */
        $claim = $repository->find($claimId);

        $claim->setEndTime($onDateTime);

        foreach($claim->getRoles() as $playsRole)
        {
            $playsRole->setEndTime($onDateTime);
        }

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_REMOVE,
                $claim->getAccount(),
                array(
                    'character'   => $claim->getCharacter()->getName()
                ))
        );
    }

    public function endClaimsForCharacter($character)
    {
        $onDateTime = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectActiveClaimsForCharacter.sql.twig')
        );

        $query->setParameter('character', $character);

        $claims = $query->getResult();

        /* @var $claim \LaDanse\DomainBundle\Entity\Claim */
        foreach($claims as $claim)
        {
            $claim->setEndTime($onDateTime);

            /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
            foreach($claim->getRoles() as $playsRole)
            {
                $playsRole->setEndTime($onDateTime);
            }
        }

        $em->flush();
    }

    public function importCharacter($name, $level, $gameRace, $gameClass)
    {
        $importInstant = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        $character = new Character();
        $character->setName($name);
        $character->setFromTime($importInstant);

        $version = new CharacterVersion();
        $version->setCharacter($character);
        $version->setLevel($level);
        $version->setFromTime($importInstant);
        $version->setGameClass($gameClass);
        $version->setGameRace($gameRace);

        $em->persist($character);
        $em->persist($version);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_CREATE,
                null,
                array(
                    'character'   => $name,
                )
            )
        );
    }

    public function updateCharacter($id, $name, $level, $gameRace, $gameClass)
    {
        $updateInstant = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $charRepo \Doctrine\ORM\EntityRepository */
        $charRepo = $em->getRepository(Character::REPOSITORY);
        /* @var $character \LaDanse\DomainBundle\Entity\Character */
        $character = $charRepo->find($id);

        $oldCharacter = clone($character);

        /**
         * @var $oldCharacterVersion CharacterVersion
         */
        $oldCharacterVersion = null;

        foreach($character->getVersions() as $charVersion)
        {
            if (is_null($charVersion->getEndTime()))
            {
                $charVersion->setEndTime($updateInstant);

                $oldCharacterVersion = clone($charVersion);
            }
        }

        $version = new CharacterVersion();
        $version->setCharacter($character);
        $version->setLevel($level);
        $version->setFromTime($updateInstant);
        $version->setGameClass($gameClass);
        $version->setGameRace($gameRace);

        $em->persist($character);
        $em->persist($version);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHARACTER_UPDATE,
                null,
                array(
                    'oldName'   => $oldCharacter->getName(),
                    'oldLevel'  => $oldCharacterVersion->getLevel(),
                    'newName'   => $name,
                    'newLevel'  => $level
                )
            )
        );
    }

    protected function charactersToDtoArray($characters, \DateTime $onDateTime)
    {
        $charactersDto = array();

        foreach($characters as $character)
        {
            $charactersDto[] = $this->characterToDto($character, $onDateTime);
        }

        return $charactersDto;
    }

    protected function createPlaysRole($onDateTime, $claim, $role)
    {
        $playsRole = new PlaysRole();
        $playsRole->setRole($role)
                  ->setClaim($claim)
                  ->setFromTime($onDateTime);

        return $playsRole;
    }

    protected function containsRole($playsRoles, $role, \DateTime $onDateTime)
    {
        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($playsRoles as $playsRole)
        {
            if (($playsRole->isRole($role))
                and
                (($playsRole->getFromTime()->getTimestamp() <= $onDateTime->getTimestamp())
                    and (is_null($playsRole->getEndTime()) or
                        ($playsRole->getEndTime()->getTimestamp() > $onDateTime->getTimestamp())))
                )
            {
                return true;
            }

        }

        return false;
    }

    /* @var $claim \LaDanse\DomainBundle\Entity\Claim
     * @var $onDateTime \DateTime
     * @return object
     */
    protected function claimToDto($claim, \DateTime $onDateTime)
    {
        return (object)array(
            "id"          => $claim->getId(),
            "character"   => $this->characterToDto($claim->getCharacter(), $onDateTime),
            "fromTime"    => $claim->getFromTime(),
            "playsTank"   => $this->containsRole($claim->getRoles(), Role::TANK, $onDateTime),
            "playsHealer" => $this->containsRole($claim->getRoles(), Role::HEALER, $onDateTime),
            "playsDPS"    => $this->containsRole($claim->getRoles(), Role::DPS, $onDateTime),
        );
    }

    /**
     * @param Character $character
     *
     * @return object
     */
    protected function characterToDto($character, \DateTime $onDateTime)
    {
        $versions = $character->getVersions();

        $activeVersion = $versions[count($versions) - 1];

        foreach($versions as $version)
        {
            if ((($version->getFromTime() <= $onDateTime) == 0)
                and ((($version->getEndTime() > $onDateTime) == 0) or is_null($version->getEndTime())))
            {
                $activeVersion = $version;
            }
        }

        return (object)array(
            "id"    => $character->getId(),
            "fromTime"  => $character->getFromTime(),
            "name"  => $character->getName(),
            "level" => $activeVersion->getLevel(),
            "class" => (object)array(
                "id"   => $activeVersion->getGameClass()->getId(),
                "name" => $activeVersion->getGameClass()->getName()
            ),
            "race"  => (object)array(
                "id"   => $activeVersion->getGameRace()->getId(),
                "name" => $activeVersion->getGameRace()->getName()
            )
        );
    }
}