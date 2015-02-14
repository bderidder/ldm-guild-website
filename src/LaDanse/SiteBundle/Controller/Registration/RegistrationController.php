<?php

namespace LaDanse\SiteBundle\Controller\Registration;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\EventListener\Features;
use LaDanse\SiteBundle\Form\Model\RegistrationFormModel;
use LaDanse\SiteBundle\Form\Type\RegistrationFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

use JMS\DiExtraBundle\Annotation as DI;

class RegistrationController extends LaDanseController
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
     * @Route("/", name="registerProfile")
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

            if ($form->isValid() && $formModel->isValid($errors, $form, $this->getAccountService()))
            {
                $user = $this->registerUser($formModel, $request, new Response());

                $this->addToast('Registration saved, you are logged in now');

                $this->eventDispatcher->dispatch(
                    FeatureUseEvent::EVENT_NAME,
                    new FeatureUseEvent(Features::REGISTRATION_CREATE, $user)
                );

                return $this->redirect($this->generateUrl('menuIndex'));
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

        return $user;
    }
}
