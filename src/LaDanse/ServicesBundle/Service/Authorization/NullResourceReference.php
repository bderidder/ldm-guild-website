<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

class NullResourceReference extends ResourceReference
{
    public function __construct()
    {
        parent::__construct('NullResourceReference');
    }

    public function getResourceType()
    {
        return "NullResourceReference";
    }

    public function getResourceId()
    {
        return "NullResourceReference";
    }
}