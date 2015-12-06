<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

class NullResourceReference extends ResourceReference
{
    public function __construct()
    {
        parent::__construct('');
    }

    public function getResourceType()
    {
        throw new UnresolvableResourceException('NullResourceReference, cannot resolve to resource');
    }

    public function getResourceId()
    {
        throw new UnresolvableResourceException('NullResourceReference, cannot resolve to resource');
    }
}