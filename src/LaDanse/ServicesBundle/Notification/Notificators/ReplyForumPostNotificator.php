<?php

namespace LaDanse\ServicesBundle\Notification\Notificators;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ForumBundle\Entity\Post;
use LaDanse\ForumBundle\Service\ForumService;
use LaDanse\ServicesBundle\Notification\AbstractNotificator;
use LaDanse\ServicesBundle\Notification\ListFunctions;
use LaDanse\ServicesBundle\Notification\NotificationContext;
use LaDanse\ServicesBundle\Notification\NotificationTemplates;
use LaDanse\ServicesBundle\Service\SettingNames;

/**
 * @DI\Service(ReplyForumPostNotificator::SERVICE_NAME, public=true)
 */
class ReplyForumPostNotificator extends AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.ReplyForumPostNotificator';

    /**
     * @var ForumService $forumService
     * @DI\Inject(ForumService::SERVICE_NAME)
     */
    public $forumService;

    public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context)
    {
        $mailsSettings = $this->getEmailsHavingSetting(SettingNames::NOTIFICATIONS_FORUMS_POST_REPLY);

        /** @var mixed $data */
        $data = $queueItem->getData();

        $mailsPosters = $this->getEmailsFromPosterInTopic($data->topicId);

        $sharedMails = ListFunctions::getIntersection($mailsPosters, $mailsSettings);

        /** @var mixed $setting */
        foreach($sharedMails as $mail)
        {
            $this->logger->info("Checking mail for " . $mail);
            $this->logger->info("Activity was by " . $queueItem->getActivityBy()->getEmail());

            if ($mail == $queueItem->getActivityBy()->getEmail())
            {
                $this->logger->info("Not sending mail to originator of activity");

                // we don't send emails to the poster self
                continue;
            }

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

    private function getEmailsFromPosterInTopic($topicId)
    {
        $topicPosts = $this->forumService->getAllPosts($topicId);

        $mails = array();

        /** @var Post $topicPost */
        foreach($topicPosts as $topicPost)
        {
            $mails[] = $topicPost->getPoster()->getEmail();
        }

        $sortedMails = ListFunctions::sortList($mails);

        return ListFunctions::removeDuplicates($sortedMails);
    }
}