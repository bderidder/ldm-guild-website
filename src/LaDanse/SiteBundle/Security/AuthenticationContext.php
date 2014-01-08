<?php

namespace LaDanse\SiteBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Session\Session;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;

class AuthenticationContext extends ContainerAwareClass
{
    const ACCOUNT_REPOSITORY = 'LaDanseDomainBundle:Account';
    const CONTEXT_SESSION_KEY = 'LaDanseAuthenticationContextId';

    /**
     * @var \LaDanse\DomainBundle\Entity\Account
     */
	private $account = NULL;

	public function __construct(Container $container)
	{
		parent::__construct($container);

        $id = $this->getSession()->get(self::CONTEXT_SESSION_KEY);

        if (!is_null($id))
        {
            $this->account = $this->getDoctrine()->getRepository(self::ACCOUNT_REPOSITORY)->find($id);
        }
    }

    public function login($id)
    {
        $this->account = $this->getDoctrine()->getRepository(self::ACCOUNT_REPOSITORY)->find($id);

        $this->getSession()->set(self::CONTEXT_SESSION_KEY, $id);
    }

    public function logout()
    {
        $this->account = NULL;

        $this->getSession()->set(self::CONTEXT_SESSION_KEY, NULL);
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
    	return !is_null($this->account);
    }

    /**
     * @return int
     */
    public function getId()
    {
        if ($this->isAuthenticated())
        {
            return $this->getAccount()->getId();
        }
        else
        {
            return -1;
        }
    }

    /**
     * @return \LaDanse\DomainBundle\Entity\Account
     */
    public function getAccount()
    {
    	return $this->account;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    private function getSession()
    {
        $requestStack = $this->getContainer()->get('request_stack');

        /* @var \Symfony\Component\HttpFoundation\Request $request */
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
