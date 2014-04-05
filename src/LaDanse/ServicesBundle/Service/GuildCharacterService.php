<?php

namespace LaDanse\ServicesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\DomainBundle\Entity\Character,
    LaDanse\DomainBundle\Entity\CharacterVersion,
    LaDanse\DomainBundle\Entity\Claim,
    LaDanse\DomainBundle\Entity\PlaysRole,
    LaDanse\DomainBundle\Entity\Role,
    LaDanse\DomainBundle\Entity\Account;

class GuildCharacterService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.GuildCharacterService';

	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
	}

    public function getAllGuildCharacters(\DateTime $onDateTime = NULL)
    {
        if ($onDateTime == NULL)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectAllGuildCharacters.sql.twig'));
        $query->setParameter('onDateTime', $onDateTime);
        
        $characters = $query->getResult();

        $charModels = array();

        foreach($characters as $character)
        {
            $charModels[] = (object)array(
                "id"    => $character->getId(),
                "name"  => $character->getName(),
                "level" => $character->getVersions()[0]->getLevel()
            );
        }

        return $charModels;
    }

	public function getClaims($accountId, \DateTime $onDateTime = NULL)
    {
    	if ($onDateTime == NULL)
    	{
    		// when not set, initialize to right now
    		$onDateTime = new \DateTime();
    	}

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectClaimsForAccount.sql.twig'));
        $query->setParameter('accountId', $accountId);
        $query->setParameter('onDateTime', $onDateTime);
        
        $claims = $query->getResult();

        $claimsModels = array();

        foreach($claims as $claim)
        {
            $claimsModels[] = (object)array(
                "id"          => $claim->getId(),
                "name"        => $claim->getCharacter()->getName(),
                "fromTime"    => $claim->getFromTime(),
                "playsTank"   => $this->containsRole($claim->getRoles(), Role::TANK),
                "playsHealer" => $this->containsRole($claim->getRoles(), Role::HEALER),
                "playsDPS"    => $this->containsRole($claim->getRoles(), Role::DPS),
            );
        }

        return $claimsModels;
    }

    public function getClaim($claimId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectActiveClaim.sql.twig'));
        $query->setParameter('claimId', $claimId);
        
        $claims = $query->getResult();

        if (count($claims) == 0)
        {
            return NULL;
        }

        $claim = $claims[0];
        
        $claimsModel = (object)array(
            "id"          => $claim->getId(),
            "name"        => $claim->getCharacter()->getName(),
            "fromTime"    => $claim->getFromTime(),
            "playsTank"   => $this->containsRole($claim->getRoles(), Role::TANK),
            "playsHealer" => $this->containsRole($claim->getRoles(), Role::HEALER),
            "playsDPS"    => $this->containsRole($claim->getRoles(), Role::DPS),
        );
       
        return $claimsModel;
    }

    public function getAllCharacters(\DateTime $onDateTime = NULL)
    {
        if ($onDateTime == NULL)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectCharactersForAccount.sql.twig'));

        $query->setParameter('onDateTime', $onDateTime);

        $characters = $query->getResult();

        return $this->charactersToDtoArray($characters);
    }

    public function getUnclaimedCharacters(\DateTime $onDateTime = NULL)
    {
        if ($onDateTime == NULL)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectUnclaimedCharacters.sql.twig'));
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

    public function getActiveClaimsForAccount($account, \DateTime $onDateTime = NULL)
    {

    }

    public function getActiveClaimsForCharacter($character, \DateTime $onDateTime = NULL)
    {

    }

    public function createClaim($accountId, $characterId, $playsTank, $playsHealer, $playsDPS)
    {
        $onDateTime = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $characterRepo = $em->getRepository(Character::REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Character */
        $character = $characterRepo->find($characterId);

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $accountRepo = $em->getRepository(Account::REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Character */
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

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $claimRepo = $em->getRepository(Claim::REPOSITORY);
        /* @var $claim \LaDanse\DomainBundle\Entity\Claim */
        $claim = $claimRepo->find($claimId);

        foreach($claim->getRoles() as $playsRole)
        {
            if ($playsRole->isRole(Role::TANK) && !$playsTank)
            {
                $playsRole->setEndTime($onDateTime);

                $this->getLogger()->info(__CLASS__ . ' removed TANK role from claim ' . $claimId);
            }

            if (!$playsRole->isRole(Role::TANK) && $playsTank)
            {
                $em->persist($this->createPlaysRole($onDateTime, $claim, Role::TANK));

                $this->getLogger()->info(__CLASS__ . ' added TANK role to claim ' . $claimId);
            }

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
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectActiveClaimsForCharacter.sql.twig'));

        $query->setParameter('character', $character);

        $claims = $query->getResult();

        foreach($claims as $claim)
        {
            $claim->setEndTime($onDateTime);

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

    protected function charactersToDtoArray($characters)
    {
        $charactersDto = array();

        foreach($characters as $character)
        {
            $charactersDto[] = $this->characterToDto($character);
        }

        return $charactersDto;
    }

    protected function characterToDto($character)
    {
        return (object)array(
            "id"        => $character->getId(),
            "name"      => $character->getName(),
            "fromTime"  => $character->getFromTime()
        );
    }

    protected function createPlaysRole($onDateTime, $claim, $role)
    {
        $playsRole = new PlaysRole();
        $playsRole->setRole($role)
                  ->setClaim($claim)
                  ->setFromTime($onDateTime);

        return $playsRole;
    }

    protected function containsRole($playsRoles, $role)
    {
        foreach($playsRoles as $playsRole)
        {
            if ($playsRole->isRole($role))
            {
                return true;
            }
        }

        return false;
    }
}