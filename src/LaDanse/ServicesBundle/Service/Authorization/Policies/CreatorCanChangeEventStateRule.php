<?php

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class CreatorCanChangeEventStateRule extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
        return $evaluationCt->getAction() == ActivityType::EVENT_CONFIRM
            || $evaluationCt->getAction() == ActivityType::EVENT_CANCEL;
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        if ($evaluationCtx->getSubject()->isAnonymous())
        {
            return false;
        }

        /** @var Event $event */
        $event = $evaluationCtx->getResourceValue();

        $account = $evaluationCtx->getSubject()->getAccount();

        return $event->getOrganiser()->getId() == $account->getId();
    }
}