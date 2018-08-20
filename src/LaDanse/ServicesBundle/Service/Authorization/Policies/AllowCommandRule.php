<?php

namespace LaDanse\ServicesBundle\Service\Authorization\Policies;

use LaDanse\ServicesBundle\Service\Authorization\EvaluationCtx;
use LaDanse\ServicesBundle\Service\Authorization\Rule;

class AllowCommandRule extends Rule
{
    public function match(EvaluationCtx $evaluationCt)
    {
        return true;
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        return 'cli' === PHP_SAPI;
    }
}