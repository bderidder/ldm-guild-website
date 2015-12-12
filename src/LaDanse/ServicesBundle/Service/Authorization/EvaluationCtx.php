<?php

namespace LaDanse\ServicesBundle\Service\Authorization;

class EvaluationCtx
{
    /** @var SubjectReference */
    private $subject;
    /** @var string */
    private $action;
    /** @var ResourceReference */
    private $resource;
    /** @var ResourceFinder */
    private $resourceFinder;

    public function __construct(
        SubjectReference $subject,
        $action,
        ResourceReference $resource,
        ResourceFinder $resourceFinder
    )
    {
        $this->subject = $subject;
        $this->action = $action;
        $this->resource = $resource;
        $this->resourceFinder = $resourceFinder;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function isSubjectInGroup($groupName)
    {
        if ($this->subject->isAnonymous())
        {
            return false;
        }

        return true;
    }

    public function getResourceType()
    {
        return $this->resource->getResourceType();
    }

    public function getResourceId()
    {
        return $this->resource->getResourceId();
    }

    public function getResourceValue()
    {
        try
        {
            return $this->resourceFinder->getResourceValue($this->resource);
        }
        catch(\Exception $e)
        {
            throw new UnresolvableResourceException('Exception while trying to retrieve resource value', 0, $e);
        }
    }
}