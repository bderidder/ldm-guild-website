<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\ListFunctions;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;
use LaDanse\ServicesBundle\Service\SettingNames;
use LaDanse\ServicesBundle\Service\SettingsService;

/**
 * @DI\Service(UpdateEventNotificator::SERVICE_NAME, public=true)
 */
class UpdateEventNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.UpdateEventNotificator';

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
        $mailsSettings = $this->getEmailsHavingSetting(SettingNames::NOTIFICATIONS_EVENT_UPDATED);

        /** @var mixed $data */
        $data = $queueItem->getData();

        $mailsSignUps = $this->getEmailsFromSignUps($data->oldEvent->eventId);

        $sharedMails = ListFunctions::getIntersection($mailsSignUps, $mailsSettings);

        /** @var mixed $setting */
        foreach($sharedMails as $mail)
        {
            if ($mail == $queueItem->getActivityBy()->getEmail())
            {
                // we don't send emails to the creator self
                continue;
            }

            /** @var mixed $data */
            $data = $queueItem->getData();

            $this->logger->debug(
                sprintf("%s - sending email to %s for event",
                    __CLASS__,
                    $setting->account->getEmail()
                )
            );

            $context->addMail(
                $setting->account->getEmail(),
                "Event Updated - " . $data->event->name,
                array(
                    'account'      => $queueItem->getActivityBy(),
                    'activityData' => $data
                ),
                NotificationTemplates::EVENT_UPDATE
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
            $mails[] = $signUp->getAccount()->getEmail();
        }

        return ListFunctions::sortList($mails);
    }
}