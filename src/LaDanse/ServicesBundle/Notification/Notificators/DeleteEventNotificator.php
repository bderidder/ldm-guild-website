<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;
use LaDanse\ServicesBundle\Service\Settings\SettingNames;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;

/**
 * @DI\Service(DeleteEventNotificator::SERVICE_NAME, public=true)
 */
class DeleteEventNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.DeleteEventNotificator';

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
            if (!$setting->value)
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
                "Event Deleted - " . $data->event->name,
                [
                    'account'      => $queueItem->getActivityBy(),
                    'activityData' => $data
                ],
                NotificationTemplates::EVENT_DELETE
            );
        }
    }
}