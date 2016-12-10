<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

class ResourceByValue extends ResourceReference
{
    private $resourceValue;

    public function __construct($resourceType, $resourceValue)
    {
        parent::__construct($resourceType);

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