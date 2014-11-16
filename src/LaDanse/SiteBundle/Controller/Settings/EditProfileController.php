<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Form\Model\ProfileFormModel;
use LaDanse\SiteBundle\Form\Type\ProfileFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditProfileController extends LaDanseController
{
	/**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/profile", name="editProfile")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in editProfile');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $account = $authContext->getAccount();

        $formModel = new ProfileFormModel();

        $formModel->setDisplayName($account->getDisplayName());
        $formModel->setLogin($account->getUsername());
        $formModel->setEmail($account->getEmail());

        $form = $this->createForm(new ProfileFormType(), $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors, $form, $authContext->getAccount(), $this->getAccountService()))
            {
               $this->updateProfile($authContext->getAccount()->getId(),
                   $formModel->getDisplayName(), $formModel->getEmail());

               $this->addToast('Profile updated');

               return $this->redirect($this->generateUrl('editProfile'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:settings:editProfile.html.twig',
                    array('form' => $form->createView(),
                        'errors' => $errors));
            }
        }
        else
        {
            return $this->render('LaDanseSiteBundle:settings:editProfile.html.twig',
                array('form' => $form->createView()));
        }
    }

    private function updateProfile($accountId, $displayName, $email)
    {
        $accountService = $this->getAccountService();

        $accountService->updateProfile($accountId, $displayName, $email);
    }
}
