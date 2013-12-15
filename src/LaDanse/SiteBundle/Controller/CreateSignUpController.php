<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Security\AuthenticationContext;

use LaDanse\SiteBundle\Form\Model\SignUpFormModel;
use LaDanse\SiteBundle\Form\Type\SignUpFormType;

use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\ForRole;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;

/**
 * @Route("/event/{id}/signup")
*/
class CreateSignUpController extends LaDanseController
{
    const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

	/**
     * @Route("/create", name="createSignUpIndex")
     * @Template("LaDanseSiteBundle::createSignUp.html.twig")
     */
    public function indexAction(Request $request, $id)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
    		return $this->redirect($this->generateUrl('viewEventsIndex'));
    	}

        $em = $this->getDoctrine();
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        $event = $repository->find($id);

        if (null === $event)
        {
            return $this->redirect($this->generateUrl('viewEventsIndex'));
        } 

        if ($this->isCurrentUserSigned($event))
        {
            return $this->redirect($this->generateUrl('viewEventsIndex'));
        }       

        $formModel = new SignUpFormModel();

        $form = $this->createForm(new SignUpFormType(), $formModel);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $this->persistSignUp($authContext, $id, $formModel);

            return $this->redirect($this->generateUrl('viewEventsIndex'));
        }
        else
        {
            return array('event' => $event, 'form' => $form->createView());            
        }
    }

    /**
     * @Route("/createabsence", name="createAbsenceIndex")
     */
    public function createAbsenceAction(Request $request, $id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            return $this->redirect($this->generateUrl('viewEventsIndex'));
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        $event = $repository->find($id);

        if (null === $event)
        {
            return $this->redirect($this->generateUrl('viewEventsIndex'));
        } 

        if ($this->isCurrentUserSigned($event))
        {
            return $this->redirect($this->generateUrl('viewEventsIndex'));
        }

        $signUp = new SignUp();
        $signUp->setEvent($event);
        $signUp->setType(SignUpType::ABSENCE);
        $signUp->setAccount($authContext->getAccount());

        $em->persist($signUp);
        $em->flush();

        return $this->redirect($this->generateUrl('viewEventsIndex'));
    }

    private function persistSignUp(AuthenticationContext $authContext, $eventId, SignUpFormModel $formModel)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        $event = $repository->find($eventId);

        $signUp = new SignUp();
        $signUp->setEvent($event);
        $signUp->setType($formModel->getType());
        $signUp->setAccount($authContext->getAccount());

        foreach($formModel->getRoles() as $strForRole)
        {
            $forRole = new ForRole();
        
            $forRole->setSignUp($signUp);
            $forRole->setRole($strForRole);

            $em->persist($forRole);
        }
        
        $em->persist($signUp);
        $em->flush();
    }

    private function isCurrentUserSigned($event)
    {

    }
}
