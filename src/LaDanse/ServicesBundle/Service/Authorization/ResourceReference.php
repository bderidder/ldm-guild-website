<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

abstract class ResourceReference
{
    /** @var string */
    private $resourceType;

    public function __construct($resourceType)
    {
        $this->resourceType = $resourceType;
    }

    /**
     * @return string
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }
}