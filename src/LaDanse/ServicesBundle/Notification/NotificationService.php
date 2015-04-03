<?php

namespace LaDanse\ServicesBundle\Notification;

use LaDanse\DomainBundle\Entity\ActivityQueueItem;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityType;

/**
 * @DI\Service(NotificationService::SERVICE_NAME, public=true)
 */
class NotificationService
{
    const SERVICE_NAME = 'LaDanse.NotificationService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    private $notifications = null;

    public function __construct()
    {
        $this->notifications[ActivityType::FORUM_TOPIC_CREATE] = 'test';
        $this->notifications[ActivityType::FORUM_POST_CREATE]  = 'test';
        $this->notifications[ActivityType::FORUM_POST_UPDATE]  = 'test';
    }

    /**
     * @param $activityType string
     *
     * @return bool
     */
    public function hasNotificationsFor($activityType)
    {
        return array_key_exists($activityType, $this->notifications);
    }

    /**
     * @param $activityQueueItem ActivityQueueItem
     */
    public function processForNotification($activityQueueItem)
    {

    }
}