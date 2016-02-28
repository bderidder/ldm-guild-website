<?php

namespace LaDanse\ServicesBundle\Service\Mail;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\MailSend;
use Trt\SwiftCssInlinerBundle\Plugin\CssInlinerPlugin;

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

    public function sendMail($from, $to, $subject, $htmlPart)
    {
        /** @var \Swift_Mime_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($htmlPart, 'text/html');

        $message->getHeaders()->addTextHeader(
            CssInlinerPlugin::CSS_HEADER_KEY_AUTODETECT
        );

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