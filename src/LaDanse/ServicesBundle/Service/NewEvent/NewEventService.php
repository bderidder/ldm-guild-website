<?php

namespace LaDanse\ServicesBundle\Service\NewEvent;

use LaDanse\CommonBundle\Helper\LaDanseService;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\NewEvent\Query\NewGetEventQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(NewEventService::SERVICE_NAME, public=true, scope="prototype")
 */
class NewEventService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.NewEventService';

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
     * @param $eventId
     *
     * @return DTO\Event\Event
     *
     * @throws EventDoesNotExistException
     */
    public function getEventById($eventId)
    {
        /** @var NewGetEventQuery $query */
        $query = $this->get(NewGetEventQuery::SERVICE_NAME);

        $query->setEventId($eventId);

        return $query->run();
    }

    // create Event (PatchEvent)

    // update Event (PatchEvent)

    // remove Event

    // create SignUp (PatchSignUp)

    // update SignUp (PatchSignUp)

    // remove SignUp
}
