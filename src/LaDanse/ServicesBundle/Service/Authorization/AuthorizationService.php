<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Authorization;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\Authorization\Policies\PolicyCatalog;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuthorizationService
 * @package LaDanse\ServicesBundle\Service\Authorization
 *
 * @DI\Service(AuthorizationService::SERVICE_NAME, public=true)
 */
class AuthorizationService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.AuthorizationService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /** @var PolicyCatalog */
    private $policyCatalog;

    /** @var ResourceFinder */
    private $resourceFinder;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->policyCatalog = new PolicyCatalog();
        $this->resourceFinder = new ResourceFinder($this->container);
    }

    /**
     * Verify if $subject is authorized to perform $action on $resource
     *
     * @param SubjectReference $subject
     * @param string $action
     * @param ResourceReference $resource
     *
     * @return bool
     *
     * @throws NotAuthorizedException
     */
    public function allowOrThrow(SubjectReference $subject, $action, ResourceReference $resource)
    {
        try
        {
            if (!$this->evaluate($subject, $action, $resource))
            {
                $this->logger->warning(
                    __CLASS__ . ' the subject is not authorized to perform this action on this resource',
                    $this->createAuthZRequestRepresentation($subject, $action, $resource)
                );

                throw new NotAuthorizedException("The subject is not authorized to perform this action on this resource");
            }

            return true;
        }
        catch(\Exception $e)
        {
            $this->logger->warning(
                __CLASS__ . ' could not evaluate authorization request',
                [
                    "exception" => $e->getMessage(),
                    "request"   => $this->createAuthZRequestRepresentation($subject, $action, $resource)
                ]
            );

            throw new NotAuthorizedException("Could not evaluate authorization request", $e);
        }
    }

    /**
     * Verify if $subject is authorized to perform $action on $resource
     *
     * @param SubjectReference $subject
     * @param string $action
     * @param ResourceReference $resource
     *
     * @return bool
     *
     * @throws CannotEvaluateException
     */
    public function evaluate(SubjectReference $subject, $action, ResourceReference $resource)
    {
        $evaluationCtx = new EvaluationCtx(
            $subject,
            $action,
            $resource,
            $this->resourceFinder
        );

        $matchedPolicy = null;

        try
        {
            $matchedPolicy = $this->findMatchingPolicy($this->policyCatalog->getPolicies(), $evaluationCtx);
        }
        catch(AuthorizationException $e)
        {
            $this->logger->error(
                'Could not find single matching policy for this evaluation request',
                ['exception' => $e]
            );

            throw new CannotEvaluateException('Cannot evaluate because no single policy matched', 0, $e);
        }

        try
        {
            return $matchedPolicy->evaluate($evaluationCtx);
        }
        catch(AuthorizationException $e)
        {
            $this->logger->error(
                'Could not properly evaluate matching policy',
                ['exception' => $e]
            );

            throw new CannotEvaluateException('Cannot evaluate', 0, $e);
        }
    }

    private function findMatchingPolicy(array $policies, EvaluationCtx $evaluationCtx)
    {
        $matchedPolicy = null;

        /** @var PolicyTreeElement $policy */
        foreach($policies as $policy)
        {
            if ($policy->match($evaluationCtx))
            {
                if ($matchedPolicy == null)
                {
                    $matchedPolicy = $policy;
                }
                else
                {
                    throw new TooManyPoliciesMatchException('more than one top policy matched the evaluation context');
                }
            }
        }

        if ($matchedPolicy == null)
        {
            throw new NoMatchingPolicyFoundException('No matching top policy found');
        }

        return $matchedPolicy;
    }

    private function createAuthZRequestRepresentation(SubjectReference $subject, $action, ResourceReference $resource)
    {
        return [
            "subject" => [
                "id"   => $subject->getAccount()->getId(),
                "name" => $subject->getAccount()->getDisplayName(),
            ],
            "action" => $action,
            "resource" => [
                "type"      => $resource->getResourceType(),
                "id"        => $resource->getResourceId(),
                "reference" => get_class($resource)
            ]
        ];
    }
}