<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\EventListener\Features;
use LaDanse\SiteBundle\Form\Model\PasswordFormModel;
use LaDanse\SiteBundle\Form\Type\PasswordFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

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
    private $eventDispatcher;

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
                $this->changePassword($authContext->getAccount()->getUsername(), $formModel->getPasswordOne());

                $this->addToast('Password changed');

                $this->eventDispatcher->dispatch(
                    FeatureUseEvent::EVENT_NAME,
                    new FeatureUseEvent(
                        Features::SETTINGS_PASSWORD_UPDATE,
                        $this->getAuthenticationService()->getCurrentContext()->getAccount()
                    )
                );

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
                FeatureUseEvent::EVENT_NAME,
                new FeatureUseEvent(
                    Features::SETTINGS_PASSWORD_VIEW,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount()
                )
            );

            return $this->render('LaDanseSiteBundle:settings:changePassword.html.twig',
                array('form' => $form->createView()));
        }
    }

    private function changePassword($username, $newPassword)
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByUsername($username);

        if ($user == null)
        {
            return;
        }

        $user->setPlainPassword($newPassword);

        $userManager->updateUser($user);
    }
}
