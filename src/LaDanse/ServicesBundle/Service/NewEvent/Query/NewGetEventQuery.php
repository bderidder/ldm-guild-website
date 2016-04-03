<?php

namespace LaDanse\ServicesBundle\Service\NewEvent\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommonBundle\Helper\AbstractQuery;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(NewGetEventQuery::SERVICE_NAME, public=true, scope="prototype")
 */
class NewGetEventQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.NewGetEventQuery';

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

    /** @var int $eventId */
    private $eventId;

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

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    protected function validateInput()
    {
    }

    protected function runQuery()
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Entity\Event::REPOSITORY);

        /* @var $event Entity\Event */
        $event = $repository->find($this->getEventId());

        if (is_null($event))
        {
            throw new EventDoesNotExistException('Event does not exist');
        }

        return DTO\Event\EventFactory::create($event);
    }
}
