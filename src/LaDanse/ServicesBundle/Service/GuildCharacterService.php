<?php

namespace LaDanse\ServicesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\DomainBundle\Entity\Character,
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
            'SELECT c, ch ' .
            'FROM LaDanse\DomainBundle\Entity\Claim c JOIN c.character ch ' .
            'WHERE c.account = :accountId ' .
            'AND (c.fromTime <= :onDateTime AND (c.endTime >= :onDateTime OR c.endTime IS NULL))');
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
            'SELECT ' .
            '    gc ' .
            'FROM ' .
            '    LaDanse\DomainBundle\Entity\Character gc ' .
            'WHERE ' .
            '    (gc.fromTime <= :onDateTime AND (gc.endTime IS NULL OR gc.endTime >= :onDateTime)) ' .
            'ORDER BY gc.name ASC'
        );

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
            'SELECT ' .
            '    gc ' .
            'FROM ' .
            '    LaDanse\DomainBundle\Entity\Character gc ' .
            'WHERE ' .
            '    (gc.fromTime <= :onDateTime AND (gc.endTime IS NULL OR gc.endTime >= :onDateTime)) ' .
            '    AND gc NOT IN ' .
            '    ( ' .
            '        SELECT' .
            '            gc2 ' .
            '        FROM ' .
            '            LaDanse\DomainBundle\Entity\Claim cc JOIN cc.character gc2 ' .
            '        WHERE ' .
            '            (gc2.fromTime <= :onDateTime AND (gc2.endTime IS NULL OR gc2.endTime >= :onDateTime)) ' .
            '           AND ' .
            '            (cc.fromTime <= :onDateTime AND (cc.endTime IS NULL OR cc.endTime >= :onDateTime)) '.
            '    )' .
            'ORDER BY gc.name ASC'
        );

        $query->setParameter('onDateTime', $onDateTime);

        $characters = $query->getResult();

        return $this->charactersToDtoArray($characters);
    }

    public function endCharacter($characterId)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository("LaDanseDomainBundle:Character");

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

    public function endClaimsForCharacter($character)
    {
        $onDateTime = new \DateTime();

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            'SELECT ' .
            '    cc ' .
            'FROM ' .
            '    LaDanse\DomainBundle\Entity\Claim cc ' .
            'WHERE ' .
            '    cc.endTime IS NULL ' .
            '    AND ' .
            '    cc.character = :character'  
        );

        $query->setParameter('character', $character);

        $claims = $query->getResult();

        foreach($claims as $claim)
        {
            $claim->setEndTime($onDateTime);
        }

        $em->flush();
    }

    public function importCharacter($name)
    {
        $em = $this->getDoctrine()->getManager();

        $character = new Character();
        $character->setName($name);
        $character->setFromTime(new \DateTime());

        $em->persist($character);
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