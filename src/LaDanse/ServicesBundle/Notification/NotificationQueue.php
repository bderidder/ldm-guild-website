<?php

namespace LaDanse\ServicesBundle\Notification;

use Doctrine\ORM\QueryBuilder;
use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @DI\Service(NotificationQueue::SERVICE_NAME, public=true)
 */
class NotificationQueue
{
    const SERVICE_NAME = 'LaDanse.NotificationQueue';

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
     * @var $notificationService NotificationService
     * @DI\Inject(NotificationService::SERVICE_NAME)
     */
    public $notificationService;

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

        if ($this->notificationService->hasNotificationsFor($activity->getType()))
        {
            $em = $this->doctrine->getManager();

            $newQueueItem = new NotificationQueueItem();

            $newQueueItem->setActivityType($activity->getType());
            $newQueueItem->setActivityOn($activity->getActivityOn());
            $newQueueItem->setActivityBy($activity->getActivityBy());
            $newQueueItem->setData($activity->getData());

            $em->persist($newQueueItem);
            $em->flush();
        }
    }

    public function cleanQueue()
    {
        $em = $this->doctrine->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('q')
            ->from('LaDanseDomainBundle:NotificationQueueItem', 'q')
            ->where($qb->expr()->isNotNull('q.processedOn'));

        $query = $qb->getQuery();

        $query->execute();
    }

    public function processQueue()
    {
        $em = $this->doctrine->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->delete('LaDanseDomainBundle:NotificationQueueItem', 'q')
            ->where($qb->expr()->isNull('q.processedOn'));

        $query = $qb->getQuery();

        /* @var $items array */
        $items = $query->getResult();

        /* @var $item NotificationQueueItem */
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
                $this->notificationService->processForNotification($item);

                $item->setProcessedOn(new \DateTime());
            }
            catch (\Exception $e)
            {
                $this->logger->error(
                    __CLASS__ .  ' caught exception: ' . $e->getMessage(),
                    array(
                        'notificationItem' => $item,
                        'exception' => $e
                    )
                );
            }
        }

        $em->flush();
    }

    public function listQueue(OutputInterface $output)
    {

    }
}