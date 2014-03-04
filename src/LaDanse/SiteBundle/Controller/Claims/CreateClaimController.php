<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use \DateTime;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Form\Model\NewClaimFormModel;
use LaDanse\SiteBundle\Form\Type\NewClaimFormType;

use LaDanse\SiteBundle\Model\ErrorModel;

class CreateClaimController extends LaDanseController
{
    /**
     * @Route("/create", name="createClaim")
     */
    public function createAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in viewClaimsAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $accountId = $authContext->getAccount()->getId();

        $formModel = new NewClaimFormModel();

        $form = $this->createForm(new NewClaimFormType($this->getContainerInjector()), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
           $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors))
            {
                $this->createClaim($accountId, $formModel);

                $this->addToast('Character claimed');

                return $this->redirect($this->generateUrl('viewClaims'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:claims:createClaim.html.twig',
                        array('form' => $form->createView(), 'errors' => $errors));
            }
        }
        else
        {
            return $this->render('LaDanseSiteBundle:claims:createClaim.html.twig',
                        array('form' => $form->createView()));
        }   
    }

    public function createClaim($accountId, $formModel)
    {
        $this->getClaimsService()->createClaim($accountId, $formModel->getCharacter(), false, true, false);    
    }    
}
