<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;

/**
 * @DI\Service(TestNotificator::SERVICE_NAME, public=true)
 */
class TestNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.TestNotificator';

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
            "Test Notification - " . $queueItem->getActivityType(),
            [
              'account'      => $queueItem->getActivityBy(),
              'activityData' => $queueItem->getData()
            ],
            NotificationTemplates::TEST
        );
    }
}