<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Service\Authorization\ResourceFinders\EventFinderModule;
use LaDanse\ServicesBundle\Service\Authorization\ResourceFinders\ResourceFinderModule;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ResourceFinder
{
    /** @var array */
    private $resourceModules;

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->initResourceModules();
    }

    public function getResourceValue(ResourceReference $resourceReference)
    {
        if (get_class($resourceReference) == ResourceByValue::class)
        {
            /** @var ResourceByValue $resourceByValue */
            $resourceByValue = $resourceReference;

            return $resourceByValue->getResourceValue();
        }
        else if (get_class($resourceReference) == ResourceById::class)
        {
            /** @var ResourceFinderModule $resourceModule */
            $resourceModule = $this->findResourceModule($resourceReference->getResourceType());

            return $resourceModule->findResourceById($resourceReference->getResourceId());
        }
        else
        {
            throw new UnresolvableResourceException(
                'Unknown type of ResourceReference ' . get_class($resourceReference)
            );
        }
    }

    private function findResourceModule($resourceType)
    {
        $resourceModule = $this->resourceModules[$resourceType];

        if ($resourceModule == null)
        {
            throw new UnresolvableResourceException('Could not find module for ' . $resourceType);
        }

        return $this->container->get($resourceModule);
    }

    private function initResourceModules()
    {
        $this->resourceModules = array();

        $this->resourceModules[Event::class] = EventFinderModule::SERVICE_NAME;
    }
}