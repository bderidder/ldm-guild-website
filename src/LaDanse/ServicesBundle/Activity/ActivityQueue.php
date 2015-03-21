<?php

namespace LaDanse\ServicesBundle\Activity;

use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\DomainBundle\Entity\ActivityQueueItem;

/**
 * @DI\Service(ActivityQueue::SERVICE_NAME, public=true)
 */
class ActivityQueue
{
    const SERVICE_NAME = 'LaDanse.ActivityQueue';

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