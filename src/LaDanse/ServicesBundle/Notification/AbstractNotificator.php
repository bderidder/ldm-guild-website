<?php

namespace LaDanse\ServicesBundle\Notification;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(AbstractNotificator::SERVICE_NAME, public=true)
 */
abstract class AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.AbstractNotificator';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var ContainerInterface $container
     * @DI\Inject("service_container")
     */
    public $container;

    abstract public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context);
}