<?php

namespace LaDanse\CommonBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ContainerInjector
{
	private $container;

	public function __construct(Container $container)
	{
    	$this->container = $container;
	}

	public function getContainer()
	{
		return $this->container;
	}
}