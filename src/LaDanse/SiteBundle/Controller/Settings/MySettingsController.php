<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Form\Model\SettingsFormModel;
use LaDanse\SiteBundle\Form\Type\SettingsFormType;

class MySettingsController extends LaDanseController
{
	/**
     * @Route("/", name="mySettingsIndex")
     * @Template("LaDanseSiteBundle::mySettings.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in indexAction');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $formModel = new SettingsFormModel();
        $formModel->setCreateEventMail(FALSE)
                  ->setChangeEventMail(FALSE)
                  ->setCancelEventMail(FALSE);

        $form = $this->createForm(new SettingsFormType(), $formModel, array('attr' => array('class' => 'form-horizontal')));

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $this->addToast('Settings saved');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }
        else
        {
            return $this->render('LaDanseSiteBundle::mySettings.html.twig',
                    array('form' => $form->createView()));
        }   
    }
}
