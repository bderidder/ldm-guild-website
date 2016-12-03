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
        $this->topPolicies = [];

        $this->topPolicies[] = new PolicySet(
            'Edit Event Policy Set',
            ActivityType::EVENT_EDIT,
            [
                new CreatorCanEditEventRule(),
                new OfficerCanEditEventRule()
            ]
        );

        $this->topPolicies[] = new PolicySet(
            'Edit Sign-Up Policy Set',
            ActivityType::SIGNUP_EDIT,
            [
                new CreatorCanEditSignUpRule(),
                new OfficerCanEditSignUpRule()
            ]
        );

        $this->topPolicies[] = new PolicySet(
            'Event State Change Policy Set',
            ActivityType::EVENT_PUT_STATE,
            [
                new CreatorCanChangeEventStateRule()
            ]
        );

        $this->topPolicies[] = new PolicySet(
            'Game Data Policy Set - Realm',
            ActivityType::REALM_CREATE,
            [
                new AllCanCreateGameDataRule()
            ]
        );

        $this->topPolicies[] = new PolicySet(
            'Game Data Policy Set - Guild',
            ActivityType::GUILD_CREATE,
            [
                new AllCanCreateGameDataRule()
            ]
        );

        $this->topPolicies[] = new PolicySet(
            'Character Set - Claim',
            ActivityType::CLAIM_EDIT,
            [
                new ClaimerCanEditClaimRule()
            ]
        );

        $this->topPolicies[] = new PolicySet(
            'Character Set - Claim',
            ActivityType::CLAIM_REMOVE,
            [
                new ClaimerCanEditClaimRule()
            ]
        );
    }
}