<?php

namespace LaDanse\ServicesBundle\Service\Authorization\ResourceFinders;

use LaDanse\CommonBundle\Helper\LaDanseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ResourceFinderModule extends LaDanseService
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    abstract function findResourceById($resourceId);
}