<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use DateTimeZone;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Service\DTO\MapperException;
use LaDanse\ServicesBundle\Service\Event\Query\EventHydrator;

class EventMapper
{
    /**
     * @param Entity\Event $event
     * @param EventHydrator $eventHydrator
     *
     * @return Event
     */
    public static function mapSingle(Entity\Event $event, EventHydrator $eventHydrator) : DTO\Event\Event
    {
        $dtoEvent = new DTO\Event\Event();

        $dtoEvent
            ->setId($event->getId())
            ->setName($event->getName())
            ->setDescription($event->getDescription())
            ->setInviteTime(EventMapper::toRealmServerTime($event->getInviteTime()))
            ->setStartTime(EventMapper::toRealmServerTime($event->getStartTime()))
            ->setEndTime(EventMapper::toRealmServerTime($event->getEndTime()))
            ->setState($event->getFiniteState())
            ->setOrganiser(
                new DTO\Reference\AccountReference(
                    $event->getOrganiser()->getId(),
                    $event->getOrganiser()->getDisplayName())
            )
            ->setCommentGroup(
                new DTO\Reference\CommentGroupReference($event->getTopicId())
            );

        $signUpDtos = [];

        /** @var Entity\SignUp $signUp */
        foreach($eventHydrator->getSignUps($event->getId()) as $signUp)
        {
            $roles = [];

            if ($signUp->getType() != Entity\SignUpType::ABSENCE)
            {
                /** @var Entity\ForRole $role */
                foreach($eventHydrator->getForRoles($signUp->getId()) as $role)
                {
                    $roles[] = $role->getRole();
                }
            }

            $dtoSignUp = new DTO\Event\SignUp();

            $dtoSignUp
                ->setId($signUp->getId())
                ->setAccount(
                    new DTO\Reference\AccountReference(
                        $signUp->getAccount()->getId(),
                        $signUp->getAccount()->getDisplayName())
                )
                ->setType($signUp->getType())
                ->setRoles($roles);

            $signUpDtos[] = $dtoSignUp;
        }

        $dtoEvent->setSignUps($signUpDtos);

        return $dtoEvent;
    }

    public static function mapArray(array $events, EventHydrator $eventHydrator) : array
    {
        $dtoArray = [];

        foreach($events as $event)
        {
            if (!($event instanceof Entity\Event))
            {
                throw new MapperException('Element in array is not of type Entity\Event');
            }

            /** @var Entity\Event $event */
            $dtoArray[] = EventMapper::mapSingle($event, $eventHydrator);
        }

        return $dtoArray;
    }

    private static function toRealmServerTime(\DateTime $date) : \DateTime
    {
        if ((new DateTimeZone('UTC'))->getOffset($date) == 0)
        {
            return $date->setTimezone(new DateTimeZone('Europe/Paris'));
        }
        else
        {
            throw new \Exception("The DateTime return from the database was not in UTC");
        }
    }
}