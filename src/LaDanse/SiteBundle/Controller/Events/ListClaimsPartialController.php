<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\DTO\Character\Character;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameClass;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameRace;
use LaDanse\ServicesBundle\Service\DTO\GameData\Realm;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\ServicesBundle\Service\Character\CharacterService;

use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use LaDanse\SiteBundle\Common\LaDanseController;
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
     *
     * @return Response
     *
     * @Route("/{eventId}/claims/{accountId}", name="listClaims")
     */
    public function listAction($eventId, $accountId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        /** @var DTO\Event\Event $event */
        $event = null;

        try
        {
            $event = $eventService->getEventById($eventId);
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in listAction',
                ["event" => $eventId]
            );

            return $this->render(
                'LaDanseSiteBundle:events:listClaims.html.twig',
                ['error' => 'Event does not exist']
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
            ['claims' => $this->getClaims($accountId, $this->getRolesForSignUp($event, $accountId), $onDate)]
        );
    }

    private function getRolesForSignUp(DTO\Event\Event $event, $accountId)
    {
        foreach($event->getSignUps() as $signUp)
        {
            /** @var DTO\Event\SignUp $signUp */

            if ($signUp->getAccount()->getId() == $accountId)
            {
                $roles = [];

                foreach($signUp->getRoles() as $forRole)
                {
                    /** @var string $forRole */

                    $roles[] = $forRole;
                }

                return $roles;
            }
        }

        return [];
    }

    private function getClaims($accountId, $roles, $onDateTime)
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        $gameRaces = $gameDataService->getAllGameRaces();
        $gameClasses = $gameDataService->getAllGameClasses();
        $realms = $gameDataService->getAllRealms();

        /** @var CharacterService $guildCharacterService */
        $guildCharacterService = $this->get(CharacterService::SERVICE_NAME);

        $characters = $guildCharacterService->getCharactersClaimedByAccount($accountId, $onDateTime);

        $resultCharacters = [];

        /** @var Character $character */
        foreach($characters as $character)
        {
            if (($roles == null || count($roles) == 0 || $this->characterPlaysAnyRole($character, $roles)) && $character->getLevel() == 110)
            {
                $resultCharacters[] = (object)[
                    "name"   => $character->getName(),
                    "level"  => $character->getLevel(),
                    "realm"  => $this->transformRealmNameForUrl($this->resolveRealmName($realms, $character->getRealmReference())),
                    "race"   => $this->resolveGameRaceName($gameRaces, $character->getGameRaceReference()),
                    "class"  => $this->resolveGameClassName($gameClasses, $character->getGameClassReference()),
                    "roles"  => $character->getClaim()->getRoles(),
                    "raider" => $character->getClaim()->isRaider()
                ];
            }
        }

        return $resultCharacters;
    }

    private function resolveGameRaceName($gameRaces, $gameRaceReference)
    {
        /** @var GameRace $gameRace */
        foreach($gameRaces as $gameRace)
        {
            if ($gameRace->getId() == $gameRaceReference)
            {
                return $gameRace->getName();
            }
        }

        return "";
    }

    private function resolveGameClassName($gameClasses, $gameClassReference)
    {
        /** @var GameClass $gameClass */
        foreach($gameClasses as $gameClass)
        {
            if ($gameClass->getId() == $gameClassReference)
            {
                return $gameClass->getName();
            }
        }

        return "";
    }

    private function resolveRealmName($realms, $realmReference)
    {
        /** @var Realm $realm */
        foreach($realms as $realm)
        {
            if ($realm->getId() == $realmReference)
            {
                return $realm->getName();
            }
        }

        return null;
    }

    private function transformRealmNameForUrl($realmName)
    {
        return str_replace(" ", "-", strtolower($realmName));
    }

    private function characterPlaysAnyRole(Character $character, $roles)
    {
        foreach($roles as $role)
        {
            if ($character->getClaim()->playsRole($role))
            {
                return true;
            }
        }

        return false;
    }
}
