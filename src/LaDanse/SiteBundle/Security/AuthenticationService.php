<?php

namespace LaDanse\SiteBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;

class AuthenticationService extends ContainerAwareClass
{
	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
    }

    /**
     * @return \LaDanse\SiteBundle\Security\AuthenticationContext
     */
    public function getCurrentContext()
    {
        return $this->getContainer()->get('LaDanse.AuthenticationContext');
    }
}
