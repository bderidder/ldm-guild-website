<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\SiteBundle\Form\Model\CreateClaimFormModel;
use LaDanse\SiteBundle\Form\Type\CreateClaimFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

class CreateClaimController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.latte")
     */
    private $logger;

    /**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/create", name="createClaim")
     */
    public function createAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in viewClaimsAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $accountId = $authContext->getAccount()->getId();

        $formModel = new CreateClaimFormModel();

        $form = $this->createForm(new CreateClaimFormType($this->getContainerInjector()), $formModel, 
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

        $this->getGuildCharacterService()->createClaim($accountId, $formModel->getCharacter(), $tank, $healer, $dps);    
    }    
}
