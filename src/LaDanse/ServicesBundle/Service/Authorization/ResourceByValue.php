<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

class ResourceByValue extends ResourceById
{
    private $resourceValue;

    public function __construct($resourceType, $resourceId, $resourceValue)
    {
        parent::__construct($resourceType, $resourceId);

        $this->resourceValue = $resourceValue;
    }

    /**
     * @return mixed
     */
    public function getResourceValue()
    {
        return $this->resourceValue;
    }
}