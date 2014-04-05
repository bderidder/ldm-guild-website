<?php

namespace LaDanse\ServicesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\DomainBundle\Entity\GameClass,
    LaDanse\DomainBundle\Entity\GameRace;

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