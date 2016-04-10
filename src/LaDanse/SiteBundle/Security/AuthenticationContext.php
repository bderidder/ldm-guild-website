<?php

namespace LaDanse\SiteBundle\Security;

use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\ServicesBundle\Common\LaDanseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuthenticationContext
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(AuthenticationContext::SERVICE_NAME, public=true)
 */
class AuthenticationContext extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.AuthenticationContext';

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return (true === $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'));
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
