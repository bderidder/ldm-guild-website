<?php

namespace LaDanse\ServicesBundle\Notification;

class NotificationTemplates
{
    const TEST         = "test";

    const TOPIC_CREATE = "topicCreate";
    const TOPIC_REPLY  = "topicReply";

    const EVENT_CREATE = "eventCreate";
    const EVENT_UPDATE = "eventUpdate";
    const EVENT_DELETE = "eventDelete";

    const EVENT_TODAY = "eventToday";

    const SIGNUP_CREATE = "signUpCreate";
    const SIGNUP_UPDATE = "signUpUpdate";
    const SIGNUP_DELETE = "signUpDelete";

    const FEEDBACK     = "feedback";

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