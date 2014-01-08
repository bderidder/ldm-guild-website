<?php

namespace LaDanse\SiteBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\Bundle\DoctrineBundle\Registry;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;

use LaDanse\DomainBundle\Entity\Account;

class AuthenticationService extends ContainerAwareClass
{
	public function __construct(Container $container)
	{
		parent::__construct($container);
    }

    /* @return \LaDanse\SiteBundle\Security\AuthenticationContext */
    public function getCurrentContext()
    {
        return $this->getContainer()->get('LaDanse.AuthenticationContext');
    }
}
