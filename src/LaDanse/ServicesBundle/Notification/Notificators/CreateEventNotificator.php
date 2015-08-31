<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;
use LaDanse\ServicesBundle\Service\SettingNames;
use LaDanse\ServicesBundle\Service\SettingsService;

/**
 * @DI\Service(CreateEventNotificator::SERVICE_NAME, public=true)
 */
class CreateEventNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.CreateEventNotificator';

    /**
     * @var SettingsService $settingsService
     * @DI\Inject(SettingsService::SERVICE_NAME)
     */
    public $settingsService;

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        $settings = $this->settingsService->getSettingsForAllAccounts(SettingNames::NOTIFICATIONS_EVENT_CREATED);

        /** @var mixed $setting */
        foreach($settings as $setting)
        {
            if ($setting->value == 0)
            {
                // the account prefers NOT to be notified
                continue;
            }

            /** @var mixed $data */
            $data = $queueItem->getData();

            $this->logger->debug(
                sprintf("%s - sending email to %s for new event",
                    __CLASS__,
                    $setting->account->getEmail()
                )
            );

            $context->addMail(
                $setting->account->getEmail(),
                "New Event - " . $data->event->name,
                array(
                    'account'      => $queueItem->getActivityBy(),
                    'activityData' => $data
                ),
                NotificationTemplates::EVENT_CREATE
            );
        }
    }
}