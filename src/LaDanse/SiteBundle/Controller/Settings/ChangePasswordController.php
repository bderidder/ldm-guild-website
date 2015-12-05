<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Service\Account\AccountService;
use LaDanse\SiteBundle\Form\Model\PasswordFormModel;
use LaDanse\SiteBundle\Form\Type\PasswordFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class ChangePasswordController extends LaDanseController
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
    public $eventDispatcher;

	/**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/password", name="changePassword")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in changePassword');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $formModel = new PasswordFormModel();

        $form = $this->createForm(new PasswordFormType(), $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors, $form))
            {
                /** @var AccountService $accountService */
                $accountService = $this->get(AccountService::SERVICE_NAME);

                $accountService->updatePassword($authContext->getAccount()->getUsername(), $formModel->getPasswordOne());

                $this->addToast('Password changed');

                return $this->redirect($this->generateUrl('menuIndex'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:settings:changePassword.html.twig',
                    array('form' => $form->createView(),
                        'errors' => $errors));
            }
        }
        else
        {
            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::SETTINGS_PASSWORD_VIEW,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount())
            );

            return $this->render('LaDanseSiteBundle:settings:changePassword.html.twig',
                array('form' => $form->createView()));
        }
    }
}
