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
            /** @var ResourceById $resourceById */
            $resourceById = $resourceReference;

            /** @var ResourceFinderModule $resourceModule */
            $resourceModule = $this->findResourceModule($resourceById->getResourceType());

            return $resourceModule->findResourceById($resourceById->getResourceId());
        }
        else if (get_class($resourceReference) == NullResourceReference::class)
        {
            throw new UnresolvableResourceException(
                'This method should never be called on a NullResourceReference'
            );
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
            throw new UnresolvableResourceException('Could not find resource module for ' . $resourceType);
        }

        return $this->container->get($resourceModule);
    }

    private function initResourceModules()
    {
        $this->resourceModules = [];

        $this->resourceModules[Event::class] = EventFinderModule::SERVICE_NAME;
    }
}