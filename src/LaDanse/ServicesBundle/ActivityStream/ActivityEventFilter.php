<?php

namespace LaDanse\ServicesBundle\ActivityStream;

use LaDanse\ServicesBundle\Activity\ActivityType;

class ActivityEventFilter
{
    private $interestedActivities;

    /**
     * ActivityEventFilter constructor.
     */
    public function __construct()
    {
        $this->interestedActivities = array
        (
            ActivityType::EVENT_EDIT,
            ActivityType::EVENT_DELETE,
            ActivityType::EVENT_CREATE,
            ActivityType::SIGNUP_CREATE,
            ActivityType::SIGNUP_EDIT,
            ActivityType::SIGNUP_DELETE,
            ActivityType::CHARACTER_CREATE,
            ActivityType::CHARACTER_UPDATE,
            ActivityType::CHARACTER_REMOVE,
            ActivityType::CLAIM_CREATE,
            ActivityType::CLAIM_EDIT,
            ActivityType::FORUM_TOPIC_CREATE,
            ActivityType::FORUM_TOPIC_REMOVE,
            ActivityType::FORUM_TOPIC_UPDATE,
            ActivityType::FORUM_POST_CREATE,
            ActivityType::FORUM_POST_UPDATE
        );
    }

    public function isInterestingActivity($activityType)
    {
        return (in_array($activityType, $this->interestedActivities));
    }
}