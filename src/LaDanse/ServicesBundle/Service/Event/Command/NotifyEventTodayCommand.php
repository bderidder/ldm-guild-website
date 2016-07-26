<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use LaDanse\ServicesBundle\Common\AbstractCommand;


use LaDanse\ServicesBundle\Service\Comments\CommentService;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(NotifyEventTodayCommand::SERVICE_NAME, public=true, shared=false)
 */
class NotifyEventTodayCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.NotifyEventTodayCommand';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $startToday = (new \DateTime())->setTime(0, 0, 0);
        $endToday = (new \DateTime())->setTime(23, 59, 59);

        $qb->select('e')
            ->from('LaDanse\DomainBundle\Entity\Event', 'e')
            ->where('e.inviteTime >= :startToday')
            ->andWhere('e.inviteTime <= :endToday')
            ->orderBy('e.inviteTime', 'ASC');

        $qb->setParameter('startToday', $startToday)
            ->setParameter('endToday', $endToday);

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving Events ",
            array(
                "query" => $qb->getDQL()
            )
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $events = $query->getResult();

        /** @var Event $event */
        foreach ($events as $event)
        {
            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::EVENT_TODAY,
                    null,
                    array(
                        'event' => $event->toJson()
                    )
                )
            );
        }
    }
}