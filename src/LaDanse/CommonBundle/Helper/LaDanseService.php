<?php

namespace LaDanse\CommonBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\DependencyInjection\ContainerInterface;

class LaDanseService extends ContainerAware
{
	public function __construct(ContainerInterface $container)
	{
		$this->setContainer($container);
	}

	protected function getLogger()
	{
		return $this->container->get('logger');
	}

    /**
     * @return \LaDanse\SiteBundle\Security\AuthenticationService
     */
	protected function getAuthenticationService()
	{
		return $this->container->get('LaDanse.AuthenticationService');
	}

	/**
     * @return Symfony\Bundle\DoctrineBundle\Registry
     */
	protected function getDoctrine()
	{
		return $this->container->get('doctrine');
	}

    /**
     * @return \LaDanse\CommonBundle\Helper\ContainerInjector
     */
	protected function getContainerInjector()
	{
        return $this->container->get('LaDanse.ContainerInjector');
	}
}