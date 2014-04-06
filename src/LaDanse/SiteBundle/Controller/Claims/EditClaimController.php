<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use \DateTime;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Form\Model\EditClaimFormModel;
use LaDanse\SiteBundle\Form\Type\EditClaimFormType;

use LaDanse\DomainBundle\Entity\Role;

use LaDanse\SiteBundle\Model\ErrorModel;

class EditClaimController extends LaDanseController
{
    /**
     * @Route("/{claimId}/edit", name="editClaim")
     */
    public function editAction(Request $request, $claimId)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in viewClaims');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $accountId = $authContext->getAccount()->getId();

        $claimModel = $this->getGuildCharacterService()->getClaim($claimId);

        $formModel = new EditClaimFormModel($claimModel);

        $form = $this->createForm(new EditClaimFormType($this->getContainerInjector()), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
           $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors))
            {
                $this->updateClaim($claimId, $formModel);

                $this->addToast('Character claim updated');

                return $this->redirect($this->generateUrl('viewClaims'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:claims:editClaim.html.twig',
                        array('claim' => $claimModel, 'form' => $form->createView(), 'errors' => $errors));
            }
        }
        else
        {
            return $this->render('LaDanseSiteBundle:claims:editClaim.html.twig',
                        array('claim' => $claimModel, 'form' => $form->createView()));
        }   
    }

    public function updateClaim($claimId, $formModel)
    {
        $tank   = false;
        $healer = false;
        $dps    = false;

        foreach($formModel->getRoles() as $role)
        {
            switch($role)
            {
                case Role::TANK:
                    $tank = true;
                    break;
                case Role::HEALER:
                    $healer = true;
                    break;
                case Role::DPS:
                    $dps = true;
                    break;       
            }
        }

        $this->getGuildCharacterService()->updateClaim($claimId, $tank, $healer, $dps);    
    }    
}