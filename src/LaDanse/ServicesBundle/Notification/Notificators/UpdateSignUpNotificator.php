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
 * @DI\Service(UpdateSignUpNotificator::SERVICE_NAME, public=true)
 */
class UpdateSignUpNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.UpdateSignUpNotificator';

    /**
     * @var SettingsService $settingsService
     * @DI\Inject(SettingsService::SERVICE_NAME)
     */
    public $settingsService;

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        /** @var mixed $data */
        $data = $queueItem->getData();

        $settings = $this->settingsService->getSettingsForAccount($data->event->organiserId);

        if (!$this->mustNotifyOrganiser($data->event->organiserId))
        {
            // the organiser does not want to be notified when sign ups change
            return;
        }

        $setting = $settings[SettingNames::NOTIFICATIONS_SIGNUPS_CHANGED];

        $this->logger->debug(
            sprintf("%s - sending email to %s for update of sign up",
                __CLASS__,
                $setting->account->getEmail()
            )
        );

        $context->addMail(
            $setting->account->getEmail(),
            "Sign Up Changed - " . $data->event->name,
            array(
                'account'      => $queueItem->getActivityBy(),
                'activityData' => $data
            ),
            NotificationTemplates::SIGNUP_UPDATE
        );
    }

    private function mustNotifyOrganiser($organiserId)
    {
        $settings = $this->settingsService->getSettingsForAccount($organiserId);

        if (!array_key_exists(SettingNames::NOTIFICATIONS_SIGNUPS_CHANGED, $settings))
        {
            // the organiser does not want to be notified when sign ups change
            return false;
        }

        /** @var mixed $setting */
        $setting = $settings[SettingNames::NOTIFICATIONS_SIGNUPS_CHANGED];

        return $setting->value;
    }
}