<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventInvalidStateChangeException;
use LaDanse\ServicesBundle\Service\Event\SignUpDoesNotExistException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(RemoveSignUpCommand::SERVICE_NAME, public=true, shared=false)
 */
class RemoveSignUpCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.RemoveSignUpCommand';

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

    /** @var int $signUpId */
    private $signUpId;

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
     * @return int
     */
    public function getSignUpId()
    {
        return $this->signUpId;
    }

    /**
     * @param int $signUpId
     */
    public function setSignUpId($signUpId)
    {
        $this->signUpId = $signUpId;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository(SignUp::REPOSITORY);

        /* @var SignUp $signUp */
        $signUp = $repository->find($this->getSignUpId());

        if ($signUp == null)
        {
            throw new SignUpDoesNotExistException("Sign up with id " . $this->getSignUpId() . ' does not exist');
        }

        $currentDateTime = new \DateTime();
        if ($signUp->getEvent()->getInviteTime() <= $currentDateTime)
        {
            throw new EventInThePastException("Event belonging to sign up is in the past and cannot be changed");
        }

        $event = $signUp->getEvent();

        $fsm = $event->getStateMachine();

        if (!($fsm->getCurrentState() == 'Pending' || $fsm->getCurrentState() == 'Confirmed'))
        {
            throw new EventInvalidStateChangeException(
                'The event is not in Pending or Confirmed state, sign-up removals are not allowed'
            );
        }

        $em->remove($signUp);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SIGNUP_DELETE,
                $this->getAccount(),
                array(
                    'event'  => $signUp->getEvent()->toJson(),
                    'signUp' => $signUp->toJson()
                ))
        );

        $em->flush();
    }
}