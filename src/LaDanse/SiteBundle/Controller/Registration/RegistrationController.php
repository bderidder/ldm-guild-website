<?php

namespace LaDanse\SiteBundle\Controller\Registration;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Form\Model\RegistrationFormModel;
use LaDanse\SiteBundle\Form\Type\RegistrationFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class RegistrationController extends LaDanseController
{
	/**
     * @Route("/", name="registerProfile")
     * @Template("LaDanseSiteBundle:registration:registerProfile.html.twig")
     */
    public function indexAction(Request $request)
    {
        $formModel = new RegistrationFormModel();

        $form = $this->createForm(new RegistrationFormType(), $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors, $form, $this->getSettingsService()))
            {
                $this->registerUser($formModel, $request, new Response());

                $this->addToast('Registration saved');

                return $this->redirect($this->generateUrl('welcomeIndex'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:registration:registerProfile.html.twig',
                    array('form' => $form->createView(),
                        'errors' => $errors));
            }
        }
        else
        {
            return $this->render('LaDanseSiteBundle:registration:registerProfile.html.twig',
                array('form' => $form->createView()));
        }
    }

    private function registerUser(RegistrationFormModel $formModel, Request $request, Response $response)
    {
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        /* @var $user \LaDanse\DomainBundle\Entity\Account */
        $user = $userManager->createUser();

        $user->setUsername($formModel->getUsername());
        $user->setPlainPassword($formModel->getPasswordOne());
        $user->setDisplayName($formModel->getDisplayName());
        $user->setEmail($formModel->getEmail());
        $user->setEnabled(true);

        $userManager->updateUser($user);

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
    }
}
