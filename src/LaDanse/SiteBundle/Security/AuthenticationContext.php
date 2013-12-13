<?php

namespace LaDanse\SiteBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\Bundle\DoctrineBundle\Registry;

use LaDanse\CommonBundle\Helper\ContainerInjector;
use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use LaDanse\DomainBundle\Entity\Account;

class AuthenticationContext extends ContainerAwareClass
{
	private $account = NULL;

	public function __construct(ContainerInjector $ci, Request $request)
	{
		parent::__construct($ci);

        $session = new Session();
        
        $id = $session->get('LaDanseAuthenticationContextId');

        if ($id)
        {
            $this->switchUser($request, $id);
        }
    }

    public function switchUser(Request $request, $id)
    {
        $this->account = $this->getDoctrine()->getRepository('LaDanseDomainBundle:Account')->find($id);

        $session = new Session();
        $session->set('LaDanseAuthenticationContextId', $id);
    }

    public function isAuthenticated()
    {
    	return !is_null($this->account);
    }

    public function getId()
    {
        if ($this->isAuthenticated())
        {
            return $this->account->getId();
        }
        else
        {
            return -1;
        }
    }

    public function getAccount()
    {
    	return $this->account;
    }
}
