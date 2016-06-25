<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\ListFunctions;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;
use LaDanse\ServicesBundle\Service\Settings\SettingNames;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;

/**
 * @DI\Service(EventTodayNotificator::SERVICE_NAME, public=true)
 */
class EventTodayNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.EventTodayNotificator';

    /**
     * @var Registry $doctrine
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @var SettingsService $settingsService
     * @DI\Inject(SettingsService::SERVICE_NAME)
     */
    public $settingsService;

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        $mailsSettings = $this->getEmailsHavingSetting(SettingNames::NOTIFICATIONS_EVENT_TODAY);

        /** @var mixed $data */
        $data = $queueItem->getData();

        $mailsSignUps = $this->getEmailsFromSignUps($data->event->eventId);

        $sharedMails = ListFunctions::getIntersection($mailsSignUps, $mailsSettings);

        /** @var string $mail */
        foreach($sharedMails as $mail)
        {
            /** @var mixed $data */
            $data = $queueItem->getData();

            $this->logger->debug(
                sprintf("%s - sending email to %s for event reminder",
                    __CLASS__,
                    $mail
                )
            );

            $context->addMail(
                $mail,
                "Reminder ... '" . $data->event->name . "' is today!",
                array(
                    'account'      => $queueItem->getActivityBy(),
                    'activityData' => $data
                ),
                NotificationTemplates::EVENT_TODAY
            );
        }
    }

    private function getEmailsFromSignUps($eventId)
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($eventId);

        $mails = array();

        /** @var SignUp $signUp */
        foreach($event->getSignUps() as $signUp)
        {
            if ($signUp->getType() != SignUpType::ABSENCE)
            {
                $mails[] = $signUp->getAccount()->getEmail();
            }
        }

        return ListFunctions::sortList($mails);
    }
}