<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Authorization;

use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\CommonBundle\Helper\LaDanseService;

use JMS\DiExtraBundle\Annotation as DI;

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
                'Could not properly find a matching policy',
                array('exception' => $e)
            );

            throw new CannotEvaluateException('Cannot evaluate', 0, $e);
        }

        try
        {
            return $matchedPolicy->evaluate($evaluationCtx);
        }
        catch(AuthorizationException $e)
        {
            $this->logger->error(
                'Could not properly evaluate matching policy',
                array('exception' => $e)
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
}