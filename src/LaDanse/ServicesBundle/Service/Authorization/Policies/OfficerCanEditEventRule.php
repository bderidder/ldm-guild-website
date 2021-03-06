<?php

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class OfficerCanEditEventRule extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
        return $evaluationCt->getAction() == ActivityType::EVENT_EDIT
            ||
            $evaluationCt->getAction() == ActivityType::EVENT_DELETE;
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        if ($evaluationCtx->getSubject()->isAnonymous())
        {
            return false;
        }

        $account = $evaluationCtx->getSubject()->getAccount();

        return $account->getId() == 1;
    }
}