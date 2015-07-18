<?php

namespace LaDanse\ServicesBundle\Notification;

class NotificationTemplates
{
    const TOPIC_CREATE = "topicCreate";

    /**
     * @param string $templatePrefix
     *
     * @return string
     */
    static public function getHtmlTemplate($templatePrefix)
    {
        return "LaDanseServicesBundle:notifications:" . $templatePrefix . ".html.twig";
    }

    /**
     * @param string $templatePrefix
     *
     * @return string
     */
    static public function getTxtTemplate($templatePrefix)
    {
        return "LaDanseServicesBundle:notifications:" . $templatePrefix . ".txt.twig";
    }
}