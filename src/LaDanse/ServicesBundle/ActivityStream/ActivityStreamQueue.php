<?php

namespace LaDanse\ServicesBundle\ActivityStream;

use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\DomainBundle\Entity\ActivityQueueItem;
use LaDanse\ServicesBundle\Activity\ActivityEvent;

/**
 * @DI\Service(ActivityStreamQueue::SERVICE_NAME, public=true)
 */
class ActivityStreamQueue
{
    const SERVICE_NAME = 'LaDanse.ActivityStreamQueue';

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
     * @param $activity ActivityEvent
     *
     * @DI\Observe(ActivityEvent::EVENT_NAME, priority = 255)
     */
    public function onActivityEvent(ActivityEvent $activity)
    {
        $this->logger->debug(
            __CLASS__ . ' received ActivityEvent',
            array(
                'activity' => $activity->getType(),
                'data' => $activity->getData()
            )
        );

        $em = $this->doctrine->getManager();

        $newQueueItem = new ActivityQueueItem();

        $newQueueItem->setActivityType($activity->getType());
        $newQueueItem->setActivityOn($activity->getActivityOn());
        $newQueueItem->setActivityBy($activity->getActivityBy());
        $newQueueItem->setData($activity->getData());

        $em->persist($newQueueItem);
        $em->flush();
    }
}