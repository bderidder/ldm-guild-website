<?php

namespace LaDanse\ServicesBundle\Notification;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Notification\Notificators\AllForumPostNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\CreateTopicNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\ReplyForumPostNotificator;
use LaDanse\ServicesBundle\Notification\Notificators\TestNotificator;
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
        if ($context->mailCount() == 0)
        {
            $this->logger->debug(
                sprintf("%s - no mail to send",
                    __CLASS__
                )
            );

            return;
        }

        $mails = $context->getMails();

        /** @var object $mail */
        foreach($mails as $mail)
        {
            $this->logger->debug(
                sprintf("%s - sending email to %s with subject '%s'",
                    __CLASS__,
                    $mail->email,
                    $mail->subject
                )
            );

            $message = \Swift_Message::newInstance()
                ->setSubject($mail->subject)
                ->setFrom(array('noreply@ladanse.org' => 'La Danse Macabre'))
                ->setTo($mail->email)
                ->setBody($this->renderView(
                    NotificationTemplates::getTxtTemplate($mail->templatePrefix),
                    array(
                        'notificationItem' => $notificationQueueItem,
                        'data'             => $mail->data
                    )
                ), 'text/plain; charset=utf-8')
                ->addPart($this->renderView(
                    NotificationTemplates::getHtmlTemplate($mail->templatePrefix),
                    array(
                        'notificationItem' => $notificationQueueItem,
                        'data'             => $mail->data
                    )
                ), 'text/html; charset=utf-8');

            $this->container->get('mailer')->send($message);
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
            CreateTopicNotificator::SERVICE_NAME,
            TestNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::FORUM_POST_CREATE]  = [
            ReplyForumPostNotificator::SERVICE_NAME,
            AllForumPostNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::EVENT_CREATE]  = [
            TestNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::EVENT_DELETE]  = [
            TestNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::SIGNUP_CREATE]  = [
            TestNotificator::SERVICE_NAME
        ];

        $this->notificators[ActivityType::SIGNUP_EDIT]  = [
            TestNotificator::SERVICE_NAME
        ];
    }
}