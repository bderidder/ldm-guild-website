<?php

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class OfficerCanEditEventRule extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        return false;
    }
}