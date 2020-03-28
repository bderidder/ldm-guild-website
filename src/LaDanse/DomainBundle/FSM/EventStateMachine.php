<?php

namespace LaDanse\DomainBundle\FSM;

use Finite\State\State;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;

class EventStateMachine
{
    const PENDING     = "Pending";
    const CONFIRMED   = "Confirmed";
    const CANCELLED   = "Cancelled";
    const NOTHAPPENED = "NotHappened";
    const HAPPENED    = "Happened";
    const DELETED     = "Deleted";
    const ARCHIVED    = "Archived";

    const TR_CONFIRM = "confirm";
    const TR_CANCEL = "cancel";
    const TR_CONFIRM_HAPPENED = "confirmHappened";
    const TR_CONFIRM_NOT_HAPPENED = "confirmNotHappened";
    const TR_ARCHIVE = "archive";
    const TR_DELETE = "delete";

    /**
     * @return StateMachine
     */
    public static function create($object)
    {
        $sm = new StateMachine($object);

        // Define states
        $sm->addState(new State(EventStateMachine::PENDING, StateInterface::TYPE_INITIAL));
        $sm->addState(EventStateMachine::CONFIRMED);
        $sm->addState(EventStateMachine::CANCELLED);
        $sm->addState(EventStateMachine::NOTHAPPENED);
        $sm->addState(EventStateMachine::HAPPENED);
        $sm->addState(new State(EventStateMachine::DELETED, StateInterface::TYPE_FINAL));
        $sm->addState(new State(EventStateMachine::ARCHIVED, StateInterface::TYPE_FINAL));

        // Define transitions
        $sm->addTransition(EventStateMachine::TR_CONFIRM,              EventStateMachine::PENDING,     EventStateMachine::CONFIRMED);
        $sm->addTransition(EventStateMachine::TR_CANCEL,               EventStateMachine::PENDING,     EventStateMachine::CANCELLED);
        $sm->addTransition(EventStateMachine::TR_CANCEL,               EventStateMachine::CONFIRMED,   EventStateMachine::CANCELLED);
        $sm->addTransition(EventStateMachine::TR_CONFIRM_HAPPENED,     EventStateMachine::CONFIRMED,   EventStateMachine::HAPPENED);
        $sm->addTransition(EventStateMachine::TR_CONFIRM_NOT_HAPPENED, EventStateMachine::CONFIRMED,   EventStateMachine::NOTHAPPENED);
        $sm->addTransition(EventStateMachine::TR_ARCHIVE,              EventStateMachine::PENDING,     EventStateMachine::ARCHIVED);
        $sm->addTransition(EventStateMachine::TR_ARCHIVE,              EventStateMachine::CANCELLED,   EventStateMachine::ARCHIVED);
        $sm->addTransition(EventStateMachine::TR_ARCHIVE,              EventStateMachine::NOTHAPPENED, EventStateMachine::ARCHIVED);
        $sm->addTransition(EventStateMachine::TR_ARCHIVE,              EventStateMachine::HAPPENED,    EventStateMachine::ARCHIVED);
        $sm->addTransition(EventStateMachine::TR_DELETE,               EventStateMachine::PENDING,     EventStateMachine::DELETED);

        return $sm;
    }
}