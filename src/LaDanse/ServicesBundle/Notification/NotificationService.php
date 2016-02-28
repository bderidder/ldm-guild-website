<?php

namespace LaDanse\ServicesBundle\Notification;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Mail\MailService;
use LaDanse\ServicesBundle\Notification\Notificators\AllForumPostNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\CreateEventNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\CreateSignUpNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\CreateTopicNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\DeleteEventNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\DeleteSignUpNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\FeedbackNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\ReplyForumPostNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\TestNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\UpdateEventNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\UpdateSignUpNotificator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(NotificationService::SERVICE_NAME, public=true)
 */
class NotificationService
{
    const SERVICE_NAME = 'LaDanse.NotificationService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @var MailService $mailService
     * @DI\Inject(MailService::SERVICE_NAME)
     */
    public $mailService;

    /**
     * @var ContainerInterface $container
     * @DI\Inject("service_container")
     */
    public $container;

    private $notificators = null;

    public function __construct()
    {
        $this->initNotificators();
    }

    /**
     * @param $activityType string
     *
     * @return bool
     */
    public function hasNotificationsFor($activityType)
    {
        return array_key_exists($activityType, $this->notificators);
    }

    /**
     * @param NotificationQueueItem $notificationQueueItem
     */
    public function processForNotification(NotificationQueueItem $notificationQueueItem)
    {
        if ($this->hasNotificationsFor($notificationQueueItem->getActivityType()))
        {
            $serviceNames = $this->notificators[$notificationQueueItem->getActivityType()];

            /* NotificationContext is a prototype service, every get() will return a new instance */
            /** @var NotificationContext $notificationContext */
            $notificationContext = $this->container->get(NotificationContext::SERVICE_NAME);

            /* @var string $serviceName */
            foreach($serviceNames as $serviceName)
            {
                /** @var AbstractNotificator $notificator */
                $notificator = $this->container->get($serviceName);

                $notificator->processNotificationItem($notificationQueueItem, $notificationContext);
            }

            $this->sendMailsFromContext($notificationQueueItem, $notificationContext);
        }
    }

    private function sendMailsFromContext(NotificationQueueItem $notificationQueueItem, NotificationContext $context)
    {
        $kernel = $this->container->get('kernel');

        if ($context->mailCount() == 0)
        {
            $this->logger->debug(
                sprintf("%s - no mail to send",
                    __CLASS__
                )
            );

            return;
        }

        $fromName = $notificationQueueItem->getActivityBy()->getDisplayName() . " (La Danse)";

        $mails = $context->getMails();

        /** @var object $mail */
        foreach($mails as $mail)
        {
            if ((strcasecmp($mail->email, $notificationQueueItem->getActivityBy()->getEmail()) == 0)
                &&
                (strcasecmp($kernel->getEnvironment(), 'dev') != 0))
            {
                // we don't send emails to the originator of the activity self
                continue;
            }

            $this->logger->debug(
                sprintf("%s - sending email to %s with subject '%s'",
                    __CLASS__,
                    $mail->email,
                    $mail->subject
                )
            );

            $this->mailService->sendMail(
                array('noreply@ladanse.org' => $fromName),
                $mail->email,
                $mail->subject,
                $this->renderView(
                    NotificationTemplates::getHtmlTemplate($mail->templatePrefix),
                    array(
                        'notificationItem' => $notificationQueueItem,
                        'data'             => $mail->data
                    ))
            );
        }
    }

    /**
     * @param string $templateName
     * @param mixed $params
     *
     * @return string
     */
    protected function renderView($templateName, $params)
    {
        $twigEnvironment = $this->container->get('twig');

        return $twigEnvironment->render($templateName, $params);
    }

    private function initNotificators()
    {
        $this->notificators[ActivityType::FORUM_TOPIC_CREATE] = [
            CreateTopicNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::FORUM_POST_CREATE]  = [
            ReplyForumPostNotificator::SERVICE_NAME,
            AllForumPostNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::EVENT_CREATE]  = [
            CreateEventNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::EVENT_EDIT]  = [
            UpdateEventNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::EVENT_DELETE]  = [
            DeleteEventNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::SIGNUP_CREATE]  = [
            CreateSignUpNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::SIGNUP_EDIT]  = [
            UpdateSignUpNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::SIGNUP_DELETE]  = [
            DeleteSignUpNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::FEEDBACK_POST]  = [
            FeedbackNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::SETTINGS_NOTIF_UPDATE]  = [
            TestNotificator::SERVICE_NAME
        ];
    }
}