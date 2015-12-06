<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

class ResourceById extends ResourceReference
{
    private $resourceId;

    public function __construct($resourceType, $resourceId)
    {
        parent::__construct($resourceType);

        $this->resourceId = $resourceId;
    }

    /**
     * @return mixed
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }
}