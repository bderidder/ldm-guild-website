<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\CommentGroupReference;

class EventFactory
{
    /**
     * @param Entity\Event $event
     *
     * @return Event
     */
    public static function create(Entity\Event $event)
    {
        $factory = new EventFactory();

        return $factory->createEvent($event);
    }

    private function createEvent(Entity\Event $event)
    {
        $signUpDtos = array();

        /** @var Entity\SignUp $signUp */
        foreach($event->getSignUps() as $signUp)
        {
            $signUpDtos[] = $this->createSignUp($signUp);
        }

        return new Event(
            $event->getId(),
            $event->getName(),
            $event->getDescription(),
            new AccountReference(
                $event->getOrganiser()->getId(),
                $event->getOrganiser()->getDisplayName()),
            $event->getInviteTime(),
            $event->getStartTime(),
            $event->getEndTime(),
            $event->getFiniteState(),
            new CommentGroupReference($event->getTopicId()),
            $signUpDtos
        );
    }

    private function createSignUp(Entity\SignUp $signUp)
    {
        $roles = null;

        if ($signUp->getType() != Entity\SignUpType::ABSENCE)
        {
            $roles = array();

            /** @var Entity\ForRole $role */
            foreach($signUp->getRoles() as $role)
            {
                $roles[] = $role->getRole();
            }
        }

        return new SignUp(
            $signUp->getId(),
            new AccountReference(
                $signUp->getAccount()->getId(),
                $signUp->getAccount()->getDisplayName()),
            $signUp->getType(),
            $roles);
    }
}