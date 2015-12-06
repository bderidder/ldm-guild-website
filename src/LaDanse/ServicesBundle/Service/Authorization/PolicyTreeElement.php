<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

abstract class PolicyTreeElement
{
    abstract public function match(EvaluationCtx $evaluationCt);

    abstract public function evaluate(EvaluationCtx $evaluationCtx);
}