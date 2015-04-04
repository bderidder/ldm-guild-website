<?php

namespace LaDanse\SiteBundle\Security;

use LaDanse\CommonBundle\Helper\ContainerAwareClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class SettingsService
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(AuthenticationService::SERVICE_NAME, public=true)
 */
class AuthenticationService extends ContainerAwareClass
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
        return $this->getContainer()->get(AuthenticationContext::SERVICE_NAME);
    }
}
