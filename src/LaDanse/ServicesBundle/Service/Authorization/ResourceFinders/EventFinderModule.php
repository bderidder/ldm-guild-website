<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Authorization\ResourceFinders;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(EventFinderModule::SERVICE_NAME, public=true)
 */
class EventFinderModule extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.EventFinderModule';

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

    function findResourceById($resourceId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        return $eventService->getEventById($resourceId);
    }
}