<?php

namespace LaDanse\SiteBundle\Security;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuthenticationService extends ContainerAwareClass
{
    /**
     * @param ContainerInterface $container
     */
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
