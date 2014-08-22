<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\ErrorModel;

use LaDanse\SiteBundle\Form\Model\PasswordFormModel;
use LaDanse\SiteBundle\Form\Type\PasswordFormType;

class ChangePasswordController extends LaDanseController
{
	/**
     * @Route("/password", name="changePassword")
     * @Template("LaDanseSiteBundle:settings:changePassword.html.twig")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in changePassword');

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

                return $this->redirect($this->generateUrl('welcomeIndex'));
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
