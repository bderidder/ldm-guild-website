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
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in indexAction');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $em = $this->getDoctrine();
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        $event = $repository->find($id);

        if (null === $event)
        {
            $this->getLogger()->warn(__CLASS__ . ' the event does not exist in indexAction', array("event id" => $id));

            return $this->redirect($this->generateUrl('welcomeIndex'));
        } 

        if ($this->getCurrentUserSignUp($event))
        {
            $this->getLogger()->warn(__CLASS__ . ' the user is already subscribed to this event in indexAction', 
                array('event' => $id, 'user' => $authContext->getAccount()->getId()));

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }       

        $formModel = new SignUpFormModel();

        $form = $this->createForm(new SignUpFormType(), $formModel, array('attr' => array('class' => 'form-horizontal')));

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $this->persistSignUp($authContext, $id, $formModel);

            return $this->redirect($this->generateUrl('viewEventIndex', array('id' => $id)));
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
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in createAbsenceAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        $event = $repository->find($id);

        if (null === $event)
        {
            $this->getLogger()->warn(__CLASS__ . ' the event does not exist in createAbsenceAction', array("event id" => $id));

            return $this->redirect($this->generateUrl('welcomeIndex'));
        } 

        if ($this->getCurrentUserSignUp($event))
        {
            $this->getLogger()->warn(__CLASS__ . ' the user is already subscribed to this event in createAbsenceAction', 
                array('event' => $id, 'user' => $authContext->getAccount()->getId()));

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $signUp = new SignUp();
        $signUp->setEvent($event);
        $signUp->setType(SignUpType::ABSENCE);
        $signUp->setAccount($authContext->getAccount());

        $this->getLogger()->info(__CLASS__ . ' persisting new sign up in createAbsenceAction');

        $em->persist($signUp);
        $em->flush();

        return $this->redirect($this->generateUrl('welcomeIndex'));
    }

    /**
     * @Route("/remove", name="removeSignUpIndex")
     */
    public function removeSignUpAction(Request $request, $id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        $event = $repository->find($id);

        if (null === $event)
        {
            return $this->redirect($this->generateUrl('welcomeIndex'));
        } 

        $signUp = $this->getCurrentUserSignUp($event);

        $em->remove($signUp);
        $em->flush();

        return $this->redirect($this->generateUrl('welcomeIndex'));
    }

    private function persistSignUp(AuthenticationContext $authContext, $eventId, SignUpFormModel $formModel)
    {
        $em = $this->getDoctrine()->getManager();

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
        
        $this->getLogger()->info(__CLASS__ . ' persisting new sign up');

        $em->persist($signUp);
        $em->flush();
    }

    private function getCurrentUserSignUp($event)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT s ' .
                                  'FROM LaDanse\DomainBundle\Entity\SignUp s ' . 
                                  'WHERE s.event = :event AND s.account = :account');
        $query->setParameter('account', $account->getId());
        $query->setParameter('event', $event->getId());

        $signUps = $query->getResult();

        if (count($signUps) === 0)
        {
            return NULL;
        }
        else
        {
            return $signUps[0];
        }
    }
}
