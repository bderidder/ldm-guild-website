<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Settings\SettingNames;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Form\Model\NotificationsFormModel;
use LaDanse\SiteBundle\Form\Type\NotificationsFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class EditNotificationsController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

	/**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/notifications", name="editNotifications")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in editNotifications');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $formModel = new NotificationsFormModel();

        $this->loadSettings($formModel, $authContext->getAccount());

        $form = $this->createForm(NotificationsFormType::class, $formModel,
            ['attr' => ['class' => 'form-horizontal', 'novalidate' => '']]);

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors))
            {
                $this->saveSettings($formModel, $authContext->getAccount());

                $this->addToast('Notifications updated');

                $this->eventDispatcher->dispatch(
                    ActivityEvent::EVENT_NAME,
                    new ActivityEvent(
                        ActivityType::SETTINGS_NOTIF_UPDATE,
                        $this->getAuthenticationService()->getCurrentContext()->getAccount())
                );

                return $this->redirect($this->generateUrl('editNotifications'));
            }
            else
            {
                return $this->render(
                    'LaDanseSiteBundle:settings:editNotifications.html.twig',
                    [
                        'form' => $form->createView(),
                        'errors' => $errors
                    ]
                );
            }
        }
        else
        {
            return $this->render(
                'LaDanseSiteBundle:settings:editNotifications.html.twig',
                ['form' => $form->createView()]
            );
        }
    }

    private function loadSettings(NotificationsFormModel $settingsFormModel, Account $account)
    {
        /** @var SettingsService $settingsService */
        $settingsService = $this->get(SettingsService::SERVICE_NAME);

        $settings = $settingsService->getSettingsForAccount($account);

        // settings for Events notifications
        $settingsFormModel->setNewEvents(
            $this->getSetting($settings, SettingNames::NOTIFICATIONS_EVENT_CREATED, false));
        $settingsFormModel->setChangeSignedEvent(
            $this->getSetting($settings, SettingNames::NOTIFICATIONS_EVENT_UPDATED, false));
        $settingsFormModel->setEventToday(
            $this->getSetting($settings, SettingNames::NOTIFICATIONS_EVENT_TODAY, false));
        $settingsFormModel->setSignUpChange(
            $this->getSetting($settings, SettingNames::NOTIFICATIONS_SIGNUPS_CHANGED, false));

        // settings for Forums notifications
        $settingsFormModel->setTopicCreated(
            $this->getSetting($settings, SettingNames::NOTIFICATIONS_FORUMS_TOPIC_CREATED, false));
        $settingsFormModel->setReplyToTopic(
            $this->getSetting($settings, SettingNames::NOTIFICATIONS_FORUMS_POST_REPLY, false));
        $settingsFormModel->setAllForumPosts(
            $this->getSetting($settings, SettingNames::NOTIFICATIONS_FORUMS_ALL_POSTS, false));
    }

    private function getSetting($settings, $settingName, $defaultValue)
    {
        if (array_key_exists($settingName, $settings))
        {
            return ($settings[$settingName]->value == 1 ? true : false);
        }
        else
        {
            return $defaultValue;
        }
    }

    private function saveSettings(NotificationsFormModel $settingsFormModel, Account $account)
    {
        /** @var SettingsService $settingsService */
        $settingsService = $this->get(SettingsService::SERVICE_NAME);

        $settingsService->updateSettingsForAccount(
            $account,
            [
                (object) [
                    'name' => SettingNames::NOTIFICATIONS_EVENT_CREATED,
                    'value' => $settingsFormModel->getNewEvents() ? '1' : '0'
                ],
                (object) [
                    'name' => SettingNames::NOTIFICATIONS_EVENT_UPDATED,
                    'value' => $settingsFormModel->getChangeSignedEvent() ? '1' : '0'
                ],
                (object) [
                    'name' => SettingNames::NOTIFICATIONS_EVENT_TODAY,
                    'value' => $settingsFormModel->getEventToday() ? '1' : '0'
                ],
                (object) [
                    'name' => SettingNames::NOTIFICATIONS_SIGNUPS_CHANGED,
                    'value' => $settingsFormModel->getSignUpChange() ? '1' : '0'
                ],
                (object) [
                    'name' => SettingNames::NOTIFICATIONS_FORUMS_TOPIC_CREATED,
                    'value' => $settingsFormModel->getTopicCreated() ? '1' : '0'
                ],
                (object) [
                    'name' => SettingNames::NOTIFICATIONS_FORUMS_POST_REPLY,
                    'value' => $settingsFormModel->getReplyToTopic() ? '1' : '0'
                ],
                (object) [
                    'name' => SettingNames::NOTIFICATIONS_FORUMS_ALL_POSTS,
                    'value' => $settingsFormModel->getAllForumPosts() ? '1' : '0'
                ]
            ]
        );
    }
}
