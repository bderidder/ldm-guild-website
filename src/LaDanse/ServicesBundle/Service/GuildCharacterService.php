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

class GuildCharacterService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.GuildCharacterService';

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
            $charModels[] = $this->characterToDto($character);
        }

        return $charModels;
    }

    public function getGuildCharacter($characterName, \DateTime $onDateTime = null)
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
        $query->setParameter('characterName', $characterName);
        $query->setParameter('onDateTime', $onDateTime);
        
        $characters = $query->getResult();

        if (count($characters) == 0)
        {
            throw new \Exception('No character could be found at that time.');
        }

        $character = $characters[0];

        return $this->characterToDto($character);
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

        return $this->charactersToDtoArray($characters);
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

        return $this->charactersToDtoArray($characters);
    }

    public function endCharacter($characterId)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Character::REPOSITORY);

        $character = $repo->find($characterId);

        $character->setEndTime(new \DateTime());

        $em->flush();

        $this->endClaimsForCharacter($character);
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

        $this->getLogger()->info(__CLASS__ . ' persisting new claim');

        $em->persist($claim);
        $em->flush();
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

                    $this->getLogger()->info(__CLASS__ . ' removed TANK role from claim ' . $claimId);
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

                $this->getLogger()->info(__CLASS__ . ' removed HEALER role from claim ' . $claimId);
            }

            if (!$playsRole->isRole(Role::HEALER) && $playsHealer)
            {
                $em->persist($this->createPlaysRole($onDateTime, $claim, Role::HEALER));

                $this->getLogger()->info(__CLASS__ . ' added HEALER role to claim ' . $claimId);
            }

            if ($playsRole->isRole(Role::DPS) && !$playsDPS)
            {
                $playsRole->setEndTime($onDateTime);

                $this->getLogger()->info(__CLASS__ . ' removed DPS role from claim ' . $claimId);
            }

            if (!$playsRole->isRole(Role::DPS) && $playsDPS)
            {
                $em->persist($this->createPlaysRole($onDateTime, $claim, Role::DPS));

                $this->getLogger()->info(__CLASS__ . ' added DPS role to claim ' . $claimId);
            }
        }

        $em->flush();
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
    }

    public function updateCharacter($id, $name, $level, $gameRace, $gameClass)
    {
        $updateInstant = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $charRepo \Doctrine\ORM\EntityRepository */
        $charRepo = $em->getRepository(Character::REPOSITORY);
        /* @var $character \LaDanse\DomainBundle\Entity\Character */
        $character = $charRepo->find($id);

        foreach($character->getVersions() as $charVersion)
        {
            if (is_null($charVersion->getEndTime()))
            {
                $charVersion->setEndTime($updateInstant);
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
    }

    protected function charactersToDtoArray($characters)
    {
        $charactersDto = array();

        foreach($characters as $character)
        {
            $charactersDto[] = $this->characterToDto($character);
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
            "character"   => $this->characterToDto($claim->getCharacter()),
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
    protected function characterToDto($character)
    {
        $versions = $character->getVersions();

        return (object)array(
            "id"    => $character->getId(),
            "fromTime"  => $character->getFromTime(),
            "name"  => $character->getName(),
            "level" => $versions[0]->getLevel(),
            "class" => (object)array(
                "id"   => $versions[0]->getGameClass()->getId(),
                "name" => $versions[0]->getGameClass()->getName()
            ),
            "race"  => (object)array(
                "id"   => $versions[0]->getGameRace()->getId(),
                "name" => $versions[0]->getGameRace()->getName()
            )
        );
    }
}