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
 * @DI\Service(CreateTopicNotificator::SERVICE_NAME, public=true)
 */
class CreateTopicNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.CreateTopicNotificator';

    /**
     * @var SettingsService $settingsService
     * @DI\Inject(SettingsService::SERVICE_NAME)
     */
    public $settingsService;

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        $settings = $this->settingsService->getSettingsForAllAccounts(SettingNames::NOTIFICATIONS_FORUMS_TOPIC_CREATED);

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
                sprintf("%s - sending email to %s for topic '%s'",
                    __CLASS__,
                    $setting->account->getEmail(),
                    $data->topicSubject
                )
            );

            $context->addMail(
                $setting->account->getEmail(),
                "Forums - new topic " . $data->topicSubject,
                [
                    'account'      => $queueItem->getActivityBy(),
                    'activityData' => $queueItem->getData()
                ],
                NotificationTemplates::TOPIC_CREATE
            );
        }
    }
}