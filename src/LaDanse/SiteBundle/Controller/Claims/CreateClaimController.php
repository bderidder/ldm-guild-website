<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Form\Model\CreateClaimFormModel;
use LaDanse\SiteBundle\Form\Type\CreateClaimFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class CreateClaimController extends LaDanseController
{
    /**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/create", name="createClaim")
     */
    public function createAction(Request $request)
    {
        $accountId = $this->getAccount()->getId();

        $formModel = new CreateClaimFormModel();

        $form = $this->createForm(new CreateClaimFormType($this->container), $formModel,
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

    /**
     * @param $accountId int
     * @param $formModel CreateClaimFormModel
     */
    public function createClaim($accountId, $formModel)
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

        /** @var GuildCharacterService $guildCharacterService */
        $guildCharacterService = $this->get(GuildCharacterService::SERVICE_NAME);

        $guildCharacterService->createClaim($accountId, $formModel->getCharacter(), $tank, $healer, $dps);    
    }    
}
