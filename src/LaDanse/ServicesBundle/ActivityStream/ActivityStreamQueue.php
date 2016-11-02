<?php

namespace LaDanse\ServicesBundle\ActivityStream;

use Doctrine\ORM\QueryBuilder;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\ActivityQueueItem;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use Symfony\Component\Console\Output\OutputInterface;

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
            [
                'activity' => $activity->getType(),
                'data' => $activity->getObject()
            ]
        );

        $activityEventFilter = new ActivityEventFilter();

        if (!$activityEventFilter->isInterestingActivity($activity->getType()))
        {
            return;
        }

        $em = $this->doctrine->getManager();

        $newQueueItem = new ActivityQueueItem();

        $newQueueItem->setActivityType($activity->getType());
        $newQueueItem->setActivityOn($activity->getTime());
        $newQueueItem->setActivityBy($activity->getActor());
        $newQueueItem->setData($activity->getObject());

        $em->persist($newQueueItem);
        $em->flush();
    }

    public function cleanQueue()
    {
        $em = $this->doctrine->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('q')
            ->from('LaDanseDomainBundle:ActivityQueueItem', 'q')
            ->where($qb->expr()->isNotNull('q.processedOn'));

        $query = $qb->getQuery();

        $query->execute();
    }

    public function processQueue()
    {
        $em = $this->doctrine->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->delete('LaDanseDomainBundle:ActivityQueueItem', 'q')
            ->where($qb->expr()->isNull('q.processedOn'));

        $query = $qb->getQuery();

        /* @var $items array */
        $items = $query->getResult();

        /* @var $item ActivityQueueItem */
        foreach($items as $item)
        {
            $this->logger->debug(
                $item->getActivityType()
                . " by "
                . $item->getActivityBy()->getDisplayName()
                . " on " . $item->getActivityOn()->format("d/m/Y h:i:s")
            );

            try
            {
                // do processing

                //$item->setProcessedOn(new \DateTime());
            }
            catch (\Exception $e)
            {
                $this->logger->error(
                    __CLASS__ .  ' caught exception: ' . $e->getMessage(),
                    [
                        'activityQueueItem' => $item,
                        'exception' => $e
                    ]
                );
            }
        }

        $em->flush();
    }

    public function listQueue(OutputInterface $output)
    {

    }
}