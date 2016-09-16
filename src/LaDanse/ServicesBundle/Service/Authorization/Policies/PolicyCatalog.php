<?php

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\PolicySet;

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
        $this->topPolicies = array();

        $this->topPolicies[] = new PolicySet(
            'Edit Event Policy Set',
            ActivityType::EVENT_EDIT,
            array(
                new CreatorCanEditEventRule(),
                new OfficerCanEditEventRule()
            )
        );

        $this->topPolicies[] = new PolicySet(
            'Event State Change Policy Set',
            ActivityType::EVENT_CONFIRM,
            array(
                new CreatorCanChangeEventStateRule()
            )
        );

        $this->topPolicies[] = new PolicySet(
            'Event State Change Policy Set',
            ActivityType::EVENT_CANCEL,
            array(
                new CreatorCanChangeEventStateRule()
            )
        );

        $this->topPolicies[] = new PolicySet(
            'Game Data Policy Set - Realm',
            ActivityType::REALM_CREATE,
            array(
                new AllCanCreateGameDataRule()
            )
        );

        $this->topPolicies[] = new PolicySet(
            'Game Data Policy Set - Guild',
            ActivityType::GUILD_CREATE,
            array(
                new AllCanCreateGameDataRule()
            )
        );

        $this->topPolicies[] = new PolicySet(
            'Character Set - Claim',
            ActivityType::CLAIM_EDIT,
            array(
                new ClaimerCanEditClaimRule()
            )
        );

        $this->topPolicies[] = new PolicySet(
            'Character Set - Claim',
            ActivityType::CLAIM_REMOVE,
            array(
                new ClaimerCanEditClaimRule()
            )
        );
    }
}