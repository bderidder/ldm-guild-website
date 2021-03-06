<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\ForRole;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\DomainBundle\FSM\EventStateMachine;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\ListFunctions;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;
use LaDanse\ServicesBundle\Service\Settings\SettingNames;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;

/**
 * @DI\Service(EventStateChangeNotificator::SERVICE_NAME, public=true)
 */
class EventStateChangeNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.EventStateChangeNotificator';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var SettingsService $settingsService
     * @DI\Inject(SettingsService::SERVICE_NAME)
     */
    public $settingsService;

    /**
     * @var Registry $doctrine
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        /** @var mixed $data */
        $data = $queueItem->getData();

        $notificationSubject = null;
        $notificationTemplate = null;

        if ($data->putEventState->state == EventStateMachine::CONFIRMED)
        {
            $notificationSubject = "Event Confirmed - " . $data->newEvent->name;
            $notificationTemplate = NotificationTemplates::EVENT_CONFIRMED;
        }
        else if ($data->putEventState->state == EventStateMachine::CANCELLED)
        {
            $notificationSubject = "Event Cancelled - " . $data->newEvent->name;
            $notificationTemplate = NotificationTemplates::EVENT_CANCELLED;
        }
        else
        {
            return;
        }

        $mailsSettings = $this->getEmailsHavingSetting(SettingNames::NOTIFICATIONS_EVENT_UPDATED);

        $mailsSignUps = $this->getEmailsFromSignUps($data->newEvent->id);

        /*
         * Mails will be sent if you have signed up (will or might come), regardless of other settings.
         */

        //$sharedMails = ListFunctions::getIntersection($mailsSignUps, $mailsSettings);
        $sharedMails = $mailsSignUps;

        /** @var mixed $setting */
        foreach($sharedMails as $mail)
        {
            /** @var mixed $data */
            $data = $queueItem->getData();

            $this->logger->debug(
                sprintf("%s - sending email to %s for event",
                    __CLASS__,
                    $mail
                )
            );

            $context->addMail(
                $mail,
                $notificationSubject,
                [
                    'account'      => $queueItem->getActivityBy(),
                    'activityData' => $data
                ],
                $notificationTemplate
            );
        }
    }

    private function getEmailsFromSignUps($eventId)
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($eventId);

        $mails = [];

        /** @var SignUp $signUp */
        foreach($event->getSignUps() as $signUp)
        {
            /*
             * Only include people who have said they would or might come.
             *
             * People who have already signed absent should not be actively notified that the
             * event has been cancelled or confirmed.
             */
            if ($signUp->getType() != SignUpType::ABSENCE)
            {
                $mails[] = $signUp->getAccount()->getEmail();
            }
        }

        return ListFunctions::sortList($mails);
    }
}