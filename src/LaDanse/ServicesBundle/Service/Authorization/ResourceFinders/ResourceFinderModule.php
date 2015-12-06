<?php

namespace LaDanse\ServicesBundle\Service\Authorization\ResourceFinders;

use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\CommonBundle\Helper\LaDanseService;

abstract class ResourceFinderModule extends LaDanseService
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    abstract function findResourceById($resourceId);
}