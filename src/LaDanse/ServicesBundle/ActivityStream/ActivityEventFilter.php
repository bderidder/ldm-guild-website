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
        $this->interestedActivities = [
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
            ActivityType::CLAIM_REMOVE,
            ActivityType::FORUM_TOPIC_CREATE,
            ActivityType::FORUM_TOPIC_REMOVE,
            ActivityType::FORUM_TOPIC_UPDATE,
            ActivityType::FORUM_POST_CREATE,
            ActivityType::FORUM_POST_UPDATE,
            ActivityType::SETTINGS_PROFILE_UPDATE,
            ActivityType::SETTINGS_PASSWORD_UPDATE,
            ActivityType::SETTINGS_CALEXPORT_UPDATE,
            ActivityType::SETTINGS_CALEXPORT_RESET,
            ActivityType::SETTINGS_NOTIF_UPDATE,
            ActivityType::BATTLENET_OAUTH_VERIFY,
            ActivityType::BATTLENET_OAUTH_DISCONNECT,
            ActivityType::REDIRECT
        ];
    }

    public function isInterestingActivity($activityType)
    {
        return (in_array($activityType, $this->interestedActivities));
    }
}