<?php

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class CreatorCanEditEventRule extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        /** @var Event $event */
        $event = $evaluationCtx->getResourceValue();

        if ($evaluationCtx->getSubject()->isAnonymous())
        {
            return false;
        }

        $account = $evaluationCtx->getSubject()->getAccount();

        return $event->getOrganiser()->getId() == $account->getId();
    }
}