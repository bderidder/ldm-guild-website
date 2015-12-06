<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

class PolicySet extends AbstractPolicy
{
    /** @var $string */
    private $name;
    /** @var array */
    private $children;
    /** @var string */
    private $target;
    /** @var bool */
    private $default;

    /**
     * PolicySet constructor.
     *
     * @param string $name
     * @param string $target
     * @param array $children
     * @param bool $default
     */
    public function __construct($name, $target, $children, $default = false)
    {
        $this->name = $name;
        $this->children = $children;
        $this->target = $target;
        $this->default = $default;
    }

    public function match(EvaluationCtx $evaluationCtx)
    {
        return $evaluationCtx->getAction() == $this->target;
    }

    public function evaluate(EvaluationCtx $evaluationCtx)
    {
        $evalResult = $this->default;

        /** @var PolicyTreeElement $childPolicy */
        foreach($this->children as $childPolicy)
        {
            $evalResult = $evalResult || $childPolicy->evaluate($evaluationCtx);
        }

        return $evalResult;
    }
}