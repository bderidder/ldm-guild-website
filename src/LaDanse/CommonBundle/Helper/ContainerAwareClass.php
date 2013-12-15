<?php

namespace LaDanse\CommonBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use LaDanse\CommonBundle\Helper\ContainerInjector;

class ContainerAwareClass
{
	private $container;

	public function __construct(Container $container)
	{
    	$this->container = $container;
	}

	protected function getContainer()
	{
		return $this->container;
	}

	protected function get($id)
	{
		return $this->getContainer()->get($id);
	}

	protected function getDoctrine()
	{
		return $this->getContainer()->get('doctrine');
	}

	protected function getLogger()
	{
		return $this->getContainer()->get('logger');
	}

	protected function getAuthenticationService()
	{
		return $this->get('LaDanse.AuthenticationService');
	}
}