<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use LaDanse\DomainBundle\Entity\ForRole;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\SiteBundle\Form\Model\SignUpFormModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\CommonBundle\Helper\AbstractCommand;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(UpdateSignUpCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class UpdateSignUpCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.UpdateSignUpCommand';

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
    /** @var SignUpFormModel $formModel */
    private $formModel;

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

    /**
     * @return SignUpFormModel
     */
    public function getFormModel()
    {
        return $this->formModel;
    }

    /**
     * @param SignUpFormModel $formModel
     */
    public function setFormModel($formModel)
    {
        $this->formModel = $formModel;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(SignUp::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $signUp = $repository->find($this->getSignUpId());

        $oldSignUpJson = $signUp->toJson();

        $signUp->setType($this->getFormModel()->getType());

        foreach($signUp->getRoles() as $origRole)
        {
            $em->remove($origRole);
        }

        $signUp->getRoles()->clear();

        if ($this->getFormModel()->getType() != SignUpType::ABSENCE)
        {
            foreach($this->getFormModel()->getRoles() as $strForRole)
            {
                $forRole = new ForRole();

                $forRole->setSignUp($signUp);
                $forRole->setRole($strForRole);

                $signUp->addRole($forRole);

                $em->persist($forRole);
            }
        }

        $this->logger->info(__CLASS__ . ' update sign up');

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SIGNUP_EDIT,
                $this->getAccount(),
                array(
                    'event'     => $signUp->getEvent()->toJson(),
                    'oldSignUp' => $oldSignUpJson,
                    'newSignUp' => $signUp->toJson()
                ))
        );
    }
}