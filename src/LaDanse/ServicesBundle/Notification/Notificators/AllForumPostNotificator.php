<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;
use LaDanse\ServicesBundle\Service\SettingNames;

/**
 * @DI\Service(AllForumPostNotificator::SERVICE_NAME, public=true)
 */
class AllForumPostNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.AllForumPostNotificator';

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        $mailsSettings = $this->getEmailsHavingSetting(SettingNames::NOTIFICATIONS_FORUMS_ALL_POSTS);

        /** @var mixed $data */
        $data = $queueItem->getData();

        /** @var mixed $setting */
        foreach($mailsSettings as $mail)
        {
            $this->logger->debug(
                sprintf("%s - sending email to %s for topic '%s'",
                    __CLASS__,
                    $mail,
                    $data->topicSubject
                )
            );

            $context->addMail(
                $mail,
                sprintf("Forums - reply in '%s'",
                    $data->topicSubject
                ),
                array(
                    'account'      => $queueItem->getActivityBy(),
                    'activityData' => $queueItem->getData()
                ),
                NotificationTemplates::TOPIC_REPLY
            );
        }
    }
}