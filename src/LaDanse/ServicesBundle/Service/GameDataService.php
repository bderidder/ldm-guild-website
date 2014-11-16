<?php

namespace LaDanse\ServicesBundle\Service;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\GameClass;
use LaDanse\DomainBundle\Entity\GameRace;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GameDataService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.GameDataService';

	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
	}

    public function getAllRaces()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository(GameRace::REPOSITORY)->findAll();
    }

    public function getAllClasses()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository(GameClass::REPOSITORY)->findAll();
    }
}