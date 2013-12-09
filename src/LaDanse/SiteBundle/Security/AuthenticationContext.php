<?php

namespace LaDanse\SiteBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Bundle\DoctrineBundle\Registry;

use LaDanse\CommonBundle\Helper\ContainerInjector;
use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\DomainBundle\Entity\Account;

class AuthenticationContext extends ContainerAwareClass
{
	private $account;

	public function __construct(ContainerInjector $ci, Request $request)
	{
		parent::__construct($ci);

		$account = $this->getDoctrine()->getRepository('LaDanseDomainBundle:Account')->find(1);
    }

    public function isAuthenticated()
    {
    	return !is_null($account);
    }

    public function getAccount()
    {
    	return $account;
    }
}
