<?php

namespace LaDanse\ServicesBundle\Service\Event;

use Symfony\Component\DependencyInjection\ContainerInterface;

use \Doctrine\Bundle\DoctrineBundle\Registry;

use LaDanse\DomainBundle\Entity\Event;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class EventService
 * @package LaDanse\ServicesBundle\Service\Event
 *
 * @DI\Service(EventService::SERVICE_NAME, public=true)
 */
class EventService
{
    const SERVICE_NAME = 'LaDanse.EventService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @var $container ContainerInterface
     * @DI\Inject("service_container")
     */
    public $container;

    /**
     * Return the event with the given id
     *
     * @param $id int id of event to retrieve
     *
     * @return Event
     */
    public function getEventById($id)
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($id);

        return $event;
    }

    public function createEvent()
    {

    }

    public function updateEvent()
    {

    }

    public function removeEvent()
    {

    }

    public function createSignUp()
    {

    }

    public function updateSignUp()
    {

    }

    public function removeSignUp()
    {

    }
}