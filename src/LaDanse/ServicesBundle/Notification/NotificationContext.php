<?php

namespace LaDanse\ServicesBundle\Notification;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(NotificationContext::SERVICE_NAME, public=true, shared=false)
 */
class NotificationContext
{
    const SERVICE_NAME = 'LaDanse.NotificationContext';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /** @var array $mails  */
    private $mails = array();

    public function addMail($email, $subject, $data, $templatePrefix)
    {
        if (array_key_exists($email, $this->mails))
        {
            $this->logger->debug(
                sprintf("%s - a mail already exists for %s",
                    __CLASS__,
                    $email
                )
            );
        }
        else
        {
            $this->addMailToArray($email, $subject, $data, $templatePrefix);
        }
    }

    public function mailCount()
    {
        return count($this->mails);
    }

    public function getMails()
    {
        return array_values($this->mails);
    }

    private function addMailToArray($email, $subject, $data, $templatePrefix)
    {
        $this->logger->debug(
            sprintf("%s - adding new mail for %s with template %s",
                __CLASS__,
                $email,
                $templatePrefix
            )
        );

        $this->mails[$email] = (object) array(
            'email'          => $email,
            'subject'        => $subject,
            'data'           => $data,
            'templatePrefix' => $templatePrefix
        );
    }
}