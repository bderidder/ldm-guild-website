<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\Policies\CreatorCanEditEventRule;
use LaDanse\ServicesBundle\Service\Authorization\Policies\OfficerCanEditEventRule;

class PolicyCatalog
{
    /** @var array */
    private $topPolicies;

    public function __construct()
    {
        $this->initPolicies();
    }

    public function getPolicies()
    {
        return $this->topPolicies;
    }

    private function initPolicies()
    {
        $editEventSet = new PolicySet(
            'Edit Event Policy Set',
            ActivityType::EVENT_EDIT,
            array(
                new CreatorCanEditEventRule(),
                new OfficerCanEditEventRule()
            )
        );

        $this->topPolicies = array();

        $this->topPolicies[] = $editEventSet;
    }
}