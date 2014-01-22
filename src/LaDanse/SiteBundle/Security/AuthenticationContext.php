<?php

namespace LaDanse\SiteBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Session\Session;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;

class AuthenticationContext extends ContainerAwareClass
{
    public function __construct(Container $container)
	{
		parent::__construct($container);
    }

    // $user = $this->get('security.context')->getToken()->getUser();
        // false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')
        // IS_AUTHENTICATED_FULLY

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
    	return (true === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'));
    }

    /**
     * @return int
     */
    public function getId()
    {
        if ($this->isAuthenticated())
        {
            return $this->get('security.context')->getToken()->getUser()->getId();
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
    	return $this->get('security.context')->getToken()->getUser();
    }
}
