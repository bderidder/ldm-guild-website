<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;

/**
 * @DI\Service(FeedbackNotificator::SERVICE_NAME, public=true)
 */
class FeedbackNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.FeedbackNotificator';

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        $toEmail = $this->container->getParameter('admin_email');

        $this->logger->debug(
            sprintf("%s - sending test email to %s",
                __CLASS__,
                $toEmail
            )
        );

        $context->addMail(
            $toEmail,
            "Feedback Notification",
            [
              'account'      => $queueItem->getActivityBy(),
              'activityData' => $queueItem->getData()
            ],
            NotificationTemplates::FEEDBACK
        );
    }
}