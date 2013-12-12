<?php

namespace LaDanse\CommonBundle\Helper;

use LaDanse\CommonBundle\Helper\ContainerInjector;

class ContainerAwareClass
{
	private $containerInjector;

	public function __construct(ContainerInjector $containerInjector)
	{
    	$this->containerInjector = $containerInjector;
	}

	protected function getContainer()
	{
		return $this->containerInjector->getContainer();
	}

	protected function get(string $id)
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
}