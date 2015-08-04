<?php

namespace LaDanse\ServicesBundle\Mail;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\MailSend;

/**
 * @DI\Service(MailService::SERVICE_NAME, public=true)
 */
class MailService
{
    const SERVICE_NAME = 'LaDanse.MailService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /**
     * @var \Swift_Mailer $swiftMailer
     * @DI\Inject("mailer")
     */
    public $swiftMailer;

    public function sendMail($from, $to, $subject, $textPart, $htmlPart = null)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($textPart, 'text/plain; charset=utf-8');

        if (!is_null($htmlPart))
        {
            $message->addPart($htmlPart, 'text/html; charset=utf-8');
        }

        $this->swiftMailer->send($message);

        $em = $this->doctrine->getManager();

        $mailSend = new MailSend();

        $mailSend->setFrom($this->emailAddressToString($from));
        $mailSend->setTo($this->emailAddressToString($to));
        $mailSend->setSubject($subject);
        $mailSend->setSendOn(new \DateTime());

        $em->persist($mailSend);
        $em->flush();
    }

    private function emailAddressToString($address)
    {
        if (is_array($address))
        {
            $result = "";

            foreach($address as $mail => $name)
            {
                if ($result != "")
                {
                    $result = $result . ", ";
                }

                $result = $result . $name . " <" . $mail . ">";
            }

            return $result;
        }
        else
        {
            return $address;
        }
    }
}