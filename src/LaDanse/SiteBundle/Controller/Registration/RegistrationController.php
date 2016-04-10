<?php

namespace LaDanse\SiteBundle\Controller\Registration;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\ServicesBundle\Service\Account\AccountService;
use LaDanse\SiteBundle\Form\Model\RegistrationFormModel;
use LaDanse\SiteBundle\Form\Type\RegistrationFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends LaDanseController
{
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
                $this->registerUser($formModel, $request, new Response());

                $this->addToast('Registration saved, you are logged in now');

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
        /** @var AccountService $accountService */
        $accountService = $this->get(AccountService::SERVICE_NAME);

        return $accountService->createAccount(
            $formModel->getUsername(),
            $formModel->getPasswordOne(),
            $formModel->getDisplayName(),
            $formModel->getEmail(),
            $request,
            $response
        );
    }
}
