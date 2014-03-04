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

class ManageClaimsController extends LaDanseController
{
    /**
     * @Route("/manage", name="manageClaims")
     */
    public function manageClaimsAction(Request $request)
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

                return $this->redirect($this->generateUrl('manageClaims'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:claims:manageClaims.html.twig',
                        array('form' => $form->createView(), 'errors' => $errors));
            }
        }
        else
        {
            return $this->render('LaDanseSiteBundle:claims:manageClaims.html.twig',
                        array('form' => $form->createView()));
        }   
    }

    /*
     * A partial view, not directly viewable
     */
    public function viewClaimsPartialAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in viewClaimsAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $accountId = $authContext->getAccount()->getId();

        $claimModel = (object)array(
            "accountId" => $accountId,
            "claims"    => $this->getClaimsService()->getClaims($accountId)
        );

        return $this->render('LaDanseSiteBundle:claims:viewClaimsPartial.html.twig', 
            array('claimModel' => $claimModel));    
    }

    /**
     * @Route("/rest/{accountId}/{claimId}", name="removeClaim")
     * @Method({"GET"})
     */
    public function removeClaimAction($accountId, $claimId)
    {
    }

    /**
     * @Route("/rest/{accountId}/{claimId}", name="updateClaim")
     * @Method({"POST"})
     */
    public function updateClaimAction($accountId, $claimId)
    {
    }

    public function createClaim($accountId, $formModel)
    {
        $this->getClaimsService()->createClaim($accountId, $formModel->getCharacter(), false, true, false);    
    }    
}
