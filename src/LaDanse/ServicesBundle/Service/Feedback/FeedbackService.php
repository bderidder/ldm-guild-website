<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Feedback;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\DomainBundle\Entity\Feedback;
use LaDanse\ServicesBundle\Activity\ActivityEvent;

use LaDanse\ServicesBundle\Activity\ActivityType;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class FeedbackService
 * @package LaDanse\ServicesBundle\Service
 *
 * @DI\Service(FeedbackService::SERVICE_NAME, public=true)
 */
class FeedbackService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.FeedbackService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

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

    public function processFeedback($account, $description)
    {
        $em = $this->doctrine->getManager();

        $feedback = new Feedback();

        $feedback->setPostedBy($account);
        $feedback->setPostedOn(new \DateTime());
        $feedback->setFeedback($description);

        $em->persist($feedback);

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::FEEDBACK_POST,
                $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                array(
                    'feedback' => $description
                ))
        );
    }
}