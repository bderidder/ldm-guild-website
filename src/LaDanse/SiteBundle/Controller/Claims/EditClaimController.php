<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\SiteBundle\Form\Model\EditClaimFormModel;
use LaDanse\SiteBundle\Form\Type\EditClaimFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

class EditClaimController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @param $request Request
     * @param $claimId string
     *
     * @return Response
     *
     * @Route("/{claimId}/edit", name="editClaim")
     */
    public function editAction(Request $request, $claimId)
    {
    	$claimModel = $this->getGuildCharacterService()->getClaimForId($claimId);

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

    /**
     * @param $claimId int
     * @param $formModel EditClaimFormModel
     */
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
