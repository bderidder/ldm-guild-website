<?php

namespace LaDanse\ServicesBundle\Notification;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\NotificationQueueItem;
use LaDanse\ServicesBundle\Service\SettingsService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(AbstractNotificator::SERVICE_NAME, public=true)
 */
abstract class AbstractNotificator
{
    const SERVICE_NAME = 'LaDanse.AbstractNotificator';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var SettingsService $settingsService
     * @DI\Inject(SettingsService::SERVICE_NAME)
     */
    public $settingsService;

    /**
     * @var ContainerInterface $container
     * @DI\Inject("service_container")
     */
    public $container;

    abstract public function processNotificationItem(
        NotificationQueueItem $queueItem,
        NotificationContext $context);

    protected function getEmailsHavingSetting($settingName)
    {
        $settings = $this->settingsService->getSettingsForAllAccounts($settingName);

        $emails = array();

        /** @var mixed $setting */
        foreach($settings as $setting)
        {
            if ($setting->value)
            {
                $emails[] = $setting->account->getEmail();
            }
        }

        return ListFunctions::sortList($emails);
    }
}