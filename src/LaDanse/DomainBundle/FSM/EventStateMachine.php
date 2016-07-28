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
    const EVENTPASSED = "EventPassed";
    const DELETED     = "Deleted";
    const ARCHIVED    = "Archived";

    /**
     * @return StateMachine
     */
    public static function create()
    {
        $sm = new StateMachine();

        // Define states
        $sm->addState(new State(EventStateMachine::PENDING, StateInterface::TYPE_INITIAL));
        $sm->addState(EventStateMachine::CONFIRMED);
        $sm->addState(EventStateMachine::CANCELLED);
        $sm->addState(EventStateMachine::NOTHAPPENED);
        $sm->addState(EventStateMachine::HAPPENED);
        $sm->addState(EventStateMachine::EVENTPASSED);
        $sm->addState(new State(EventStateMachine::DELETED, StateInterface::TYPE_FINAL));
        $sm->addState(new State(EventStateMachine::ARCHIVED, StateInterface::TYPE_FINAL));

        // Define transitions
        $sm->addTransition('confirm',            EventStateMachine::PENDING,     EventStateMachine::CONFIRMED);
        $sm->addTransition('cancel',             EventStateMachine::PENDING,     EventStateMachine::CANCELLED);
        $sm->addTransition('cancel',             EventStateMachine::CONFIRMED,   EventStateMachine::CANCELLED);
        $sm->addTransition('confirmHappened',    EventStateMachine::CONFIRMED,   EventStateMachine::HAPPENED);
        $sm->addTransition('confirmNotHappened', EventStateMachine::CONFIRMED,   EventStateMachine::NOTHAPPENED);
        $sm->addTransition('timePassed',         EventStateMachine::PENDING,     EventStateMachine::EVENTPASSED);
        $sm->addTransition('timePassed',         EventStateMachine::CANCELLED,   EventStateMachine::EVENTPASSED);
        $sm->addTransition('archive',            EventStateMachine::EVENTPASSED, EventStateMachine::ARCHIVED);
        $sm->addTransition('archive',            EventStateMachine::NOTHAPPENED, EventStateMachine::ARCHIVED);
        $sm->addTransition('archive',            EventStateMachine::HAPPENED,    EventStateMachine::ARCHIVED);
        $sm->addTransition('delete',             EventStateMachine::PENDING,     EventStateMachine::DELETED);

        return $sm;
    }
}