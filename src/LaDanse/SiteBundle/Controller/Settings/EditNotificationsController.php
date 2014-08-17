<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\ErrorModel;

use LaDanse\SiteBundle\Form\Model\NotificationsFormModel;
use LaDanse\SiteBundle\Form\Type\NotificationsFormType;

class EditNotificationsController extends LaDanseController
{
	/**
     * @Route("/notifications", name="editNotifications")
     * @Template("LaDanseSiteBundle:settings:editNotifications.html.twig")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in editNotifications');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $formModel = new NotificationsFormModel();

        $form = $this->createForm(new NotificationsFormType(), $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors))
            {
                $this->addToast('Notifications updated');

                return $this->redirect($this->generateUrl('editNotifications'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:settings:editNotifications.html.twig',
                    array('form' => $form->createView(),
                        'errors' => $errors));
            }
        }
        else
        {
            return $this->render('LaDanseSiteBundle:settings:editNotifications.html.twig',
                array('form' => $form->createView()));
        }
    }
}
