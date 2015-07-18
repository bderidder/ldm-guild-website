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
                $data,
                NotificationTemplates::TOPIC_CREATE,
                100
            );
        }
    }
}