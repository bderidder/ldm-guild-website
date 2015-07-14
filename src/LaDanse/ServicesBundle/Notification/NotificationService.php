<?php

namespace LaDanse\ServicesBundle\Notification;

use LaDanse\DomainBundle\Entity\ActivityQueueItem;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
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

    private $notificators = null;

    public function __construct()
    {
        $this->initNotificators();
    }

    /**
     * @param $activityType string
     *
     * @return bool
     */
    public function hasNotificationsFor($activityType)
    {
        return array_key_exists($activityType, $this->notificators);
    }

    /**
     * @param NotificationQueueItem $notificationQueueItem
     */
    public function processForNotification(NotificationQueueItem $notificationQueueItem)
    {

    }

    private function initNotificators()
    {
        $this->notificators[ActivityType::FORUM_TOPIC_CREATE] = 'test';
        $this->notificators[ActivityType::FORUM_POST_CREATE]  = 'test';
        $this->notificators[ActivityType::FORUM_POST_UPDATE]  = 'test';
    }
}