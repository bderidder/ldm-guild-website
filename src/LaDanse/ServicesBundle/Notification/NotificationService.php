<?php

namespace LaDanse\ServicesBundle\Notification;

use JMS\DiExtraBundle\Annotation as DI;

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

    /**
     * @param $activityType string
     *
     * @return bool
     */
    protected function hasNotificationsFor($activityType)
    {
        return true;
    }
}