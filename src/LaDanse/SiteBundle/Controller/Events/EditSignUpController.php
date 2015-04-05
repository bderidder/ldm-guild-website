<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\ForRole;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\SiteBundle\Form\Model\SignUpFormModel;
use LaDanse\SiteBundle\Form\Type\SignUpFormType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/{id}/signup")
*/
class EditSignUpController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

	/**
     * @param $request Request
     * @param $id string
     *
     * @return Response
     *
     * @Route("/edit", name="editSignUp")
     */
    public function createAction(Request $request, $id)
    {
    	$em = $this->getDoctrine();
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(Event::REPOSITORY);

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

        $currentSignUp = $this->getCurrentUserSignUp($event);

        if (!$currentSignUp)
        {
            $this->logger->warning(__CLASS__ . ' the user is not yet subscribed to this event in editSignUp',
                array('event' => $id, 'user' => $this->getAccount()->getId()));

            return $this->redirect($this->generateUrl('calendarIndex'));
        }       

        $formModel = new SignUpFormModel($currentSignUp);

        $form = $this->createForm(new SignUpFormType(), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        $form->handleRequest($request);

        if ($form->isValid() && $formModel->isValid($form))
        {
            $this->updateSignUp($currentSignUp, $formModel);

            $this->addToast('Signup updated');

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        else
        {
            return $this->render("LaDanseSiteBundle:events:editSignUp.html.twig",
                array('event' => $event, 'form' => $form->createView()));
        }
    }

    private function updateSignUp(SignUp $signUp, SignUpFormModel $formModel)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $oldSignUpJson = $signUp->toJson();

        $signUp->setType($formModel->getType());

        foreach($signUp->getRoles() as $origRole)
        {
            $em->remove($origRole);
        }

        $signUp->getRoles()->clear();

        if ($formModel->getType() != SignUpType::ABSENCE)
        {
            foreach($formModel->getRoles() as $strForRole)
            {
                $forRole = new ForRole();

                $forRole->setSignUp($signUp);
                $forRole->setRole($strForRole);

                $signUp->addRole($forRole);

                $em->persist($forRole);
            }
        }

        $this->logger->info(__CLASS__ . ' update sign up');

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SIGNUP_EDIT,
                $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                array(
                    'event'     => $signUp->getEvent()->toJson(),
                    'oldSignUp' => $oldSignUpJson,
                    'newSignUp' => $signUp->toJson()
                ))
        );
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
