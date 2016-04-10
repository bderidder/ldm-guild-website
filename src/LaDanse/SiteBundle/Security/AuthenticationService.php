<?php

namespace LaDanse\SiteBundle\Security;

use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\ServicesBundle\Common\LaDanseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuthenticationService
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(AuthenticationService::SERVICE_NAME, public=true)
 */
class AuthenticationService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.AuthenticationService';

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
     * @return \LaDanse\SiteBundle\Security\AuthenticationContext
     */
    public function getCurrentContext()
    {
        return $this->container->get(AuthenticationContext::SERVICE_NAME);
    }
}
