<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\ForRole;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\SiteBundle\Form\Model\SignUpFormModel;
use LaDanse\SiteBundle\Form\Type\SignUpFormType;
use LaDanse\SiteBundle\Security\AuthenticationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/{id}/signup")
*/
class CreateSignUpController extends LaDanseController
{
    const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

	/**
     * @param $request Request
     * @param $id string
     *
     * @return Response
     *
     * @Route("/create", name="createSignUp")
     */
    public function createAction(Request $request, $id)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in indexAction');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $em = $this->getDoctrine();
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(self::EVENT_REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($id);

        if (null === $event)
        {
            $this->logger->warning(__CLASS__ . ' the event does not exist in indexAction', array("event id" => $id));

            return $this->redirect($this->generateUrl('calendarIndex'));
        } 

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }

        if ($this->getCurrentUserSignUp($event))
        {
            $this->logger->warning(__CLASS__ . ' the user is already subscribed to this event in indexAction',
                array('event' => $id, 'user' => $authContext->getAccount()->getId()));

            return $this->redirect($this->generateUrl('calendarIndex'));
        }       

        $formModel = new SignUpFormModel();

        $form = $this->createForm(new SignUpFormType(), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        $form->handleRequest($request);

        if ($form->isValid() && $formModel->isValid($form))
        {
            $this->persistSignUp($authContext, $id, $formModel);

            $this->addToast('Signed up');

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        else
        {
            return $this->render("LaDanseSiteBundle:events:createSignUp.html.twig",
                array('event' => $event, 'form' => $form->createView()));
        }
    }

    /**
     * @param $id string
     *
     * @return Response
     *
     * @Route("/createabsence", name="createAbsence")
     */
    public function createAbsenceAction($id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in createAbsenceAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($id);

        if (null === $event)
        {
            $this->logger->warning(__CLASS__ . ' the event does not exist in createAbsenceAction', array("event id" => $id));

            return $this->redirect($this->generateUrl('calendarIndex'));
        } 

        if ($this->getCurrentUserSignUp($event))
        {
            $this->logger->warning(__CLASS__ . ' the user is already subscribed to this event in createAbsenceAction',
                array('event' => $id, 'user' => $authContext->getAccount()->getId()));

            return $this->redirect($this->generateUrl('calendarIndex'));
        }

        $signUp = new SignUp();
        $signUp->setEvent($event);
        $signUp->setType(SignUpType::ABSENCE);
        $signUp->setAccount($authContext->getAccount());

        $this->logger->info(__CLASS__ . ' persisting new sign up in createAbsenceAction');

        $em->persist($signUp);
        $em->flush();

        $this->addToast('Absence saved');

        return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
    }

    private function persistSignUp(AuthenticationContext $authContext, $eventId, SignUpFormModel $formModel)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
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

        $this->logger->info(__CLASS__ . ' persisting new sign up');

        $em->persist($signUp);
        $em->flush();
    }

    private function getCurrentUserSignUp(Event $event)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
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
