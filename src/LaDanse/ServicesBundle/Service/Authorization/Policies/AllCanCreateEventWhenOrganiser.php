<?php

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;
use LaDanse\ServicesBundle\Service\DTO\Event\Event;
use LaDanse\ServicesBundle\Service\DTO\Event\PostEvent;

class AllCanCreateEventWhenOrganiser extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
        return $evaluationCt->getAction() == ActivityType::EVENT_CREATE;
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        if ($evaluationCtx->getSubject()->isAnonymous())
        {
            return false;
        }

        /** @var PostEvent $postEvent */
        $postEvent = $evaluationCtx->getResourceValue();

        $account = $evaluationCtx->getSubject()->getAccount();

        return $postEvent->getOrganiserReference()->getId() == $account->getId();
    }
}