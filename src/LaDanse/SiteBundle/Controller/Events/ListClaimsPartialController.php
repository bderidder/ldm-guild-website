<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventService;

use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\Response;

class ListClaimsPartialController extends LaDanseController
{
    const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @param string $eventId
     * @param string $accountId
     * @param string $role
     *
     * @return Response
     *
     * @Route("/{eventId}/claims/{accountId}/role/{role}", name="listClaims")
     */
    public function listAction($eventId, $accountId, $role)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        $event = null;

        try
        {
            $event = $eventService->getEventById($eventId);
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in listAction',
                array("event" => $eventId)
            );

            return $this->render(
                'LaDanseSiteBundle:events:listClaims.html.twig',
                array('error' => 'Event does not exist')
            );
        }

        $now = new \DateTime();

        if ($event->getInviteTime() < $now)
        {
            // event is in the past, use invite time
            $onDate = $event->getInviteTime();
        }
        else
        {
            $onDate = $now;
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIMS_LIST,
                $this->getAccount())
        );

        return $this->render(
            'LaDanseSiteBundle:events:listClaims.html.twig',
            array('claims' => $this->getClaims($accountId, $role, $onDate))
        );
    }

    private function getClaims($accountId, $role, $onDateTime)
    {
        /** @var GuildCharacterService $guildCharacterService */
        $guildCharacterService = $this->get(GuildCharacterService::SERVICE_NAME);

        $claims = $guildCharacterService->getClaimsForAccount($accountId, $onDateTime);

        $claimsDto = array();

        foreach($claims as $claim)
        {
            $includeClaim = false;

            switch ($role)
            {
                case "Tank":
                    if ($claim->playsTank)
                    {
                        $includeClaim = true;
                    };
                    break;
                case "Healer":
                    if ($claim->playsHealer)
                    {
                        $includeClaim = true;
                    };
                    break;
                case "DPS":
                    if ($claim->playsDPS)
                    {
                        $includeClaim = true;
                    };
                    break;
            }

            if ($includeClaim)
            {
                $character = $guildCharacterService->getGuildCharacter($claim->character->id, $onDateTime);

                $claimsDto[] = (object)array(
                    "name"  => $character->name,
                    "level" => $character->level,
                    "race"  => $character->race->name,
                    "class" => $character->class->name
                );
            }
        }

        return $claimsDto;
    }
}
