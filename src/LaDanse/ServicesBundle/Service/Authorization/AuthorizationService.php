<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Authorization;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

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
    }

    /**
     * Verify if $subject is authorized to perform $action on $resource
     *
     * @param $subject
     * @param $action
     * @param $resource
     *
     * @return bool
     */
    public function isAuthorized($subject, $action, $resource)
    {
        /*
         * Step 1. Gather relevant policies based on $action and $resource
         * Step 2. Evaluate policies
         * Step 3. Return the produced authorization result
         */

        return false;
    }
}