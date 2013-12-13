<?php

namespace LaDanse\SiteBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\Bundle\DoctrineBundle\Registry;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;

use LaDanse\DomainBundle\Entity\Account;

class AuthenticationContext extends ContainerAwareClass
{
	private $account = NULL;

	public function __construct(Container $container)
	{
		parent::__construct($container);

        $id = $this->getSession()->get('LaDanseAuthenticationContextId');

        if (!is_null($id))
        {
            $this->account = $this->getDoctrine()->getRepository('LaDanseDomainBundle:Account')->find($id);
        }
    }

    public function login($id)
    {
        $this->account = $this->getDoctrine()->getRepository('LaDanseDomainBundle:Account')->find($id);

        $this->getSession()->set('LaDanseAuthenticationContextId', $id);
    }

    public function logout()
    {
        $this->account = NULL;

        $this->getSession()->set('LaDanseAuthenticationContextId', NULL);           
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

    private function getSession()
    {
        $requestStack = $this->getContainer()->get('request_stack');
        $request = $requestStack->getCurrentRequest();

        if (!$request->hasSession())
        {
            $session = new Session();
            $request->setSession($session);

            return $session;
        }
        else
        {
            return $request->getSession();
        }
    }
}
