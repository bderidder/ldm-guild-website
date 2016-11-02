<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\ServicesBundle\Service\Authorization\NotAuthorizedException;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Form\Model\SignUpFormModel;
use LaDanse\SiteBundle\Form\Type\SignUpFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

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
     * @param $request Request
     * @param $id string
     *
     * @return Response
     *
     * @Route("/edit", name="editSignUp")
     */
    public function createAction(Request $request, $id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        /** @var Event $event */
        $event = null;

        try
        {
            $event = $eventService->getEventById($id);
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in indexAction',
                ["event" => $id]
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }

        /* verify that the event is not in the past */
        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
        }

        /** @var SignUp $currentSignUp */
        $currentSignUp = null;

        try
        {
            $currentSignUp = $eventService->getSignUpForUser(
                $event->getId(),
                $account->getId()
            );
        }
        catch(EventDoesNotExistException $e)
        {
            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        catch(EventInThePastException $e)
        {
            return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
        }
        catch(\Exception $e)
        {
            $this->logger->warning(
                __CLASS__ . ' unexpected error',
                [
                    "throwable" => $e,
                    "event"     => $id,
                    "account"   => $account->getId()
                ]
            );

            return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
        }

        if ($currentSignUp == null)
        {
            $this->logger->warning(__CLASS__ . ' the user is not yet subscribed to this event in editSignUp',
                ['event' => $id, 'user' => $this->getAccount()->getId()]);

            return $this->redirect($this->generateUrl('calendarIndex'));
        }       

        $formModel = new SignUpFormModel($currentSignUp);

        $form = $this->createForm(SignUpFormType::class, $formModel,
            ['attr' => ['class' => 'form-horizontal', 'novalidate' => '']]);

        $form->handleRequest($request);

        if ($form->isValid() && $formModel->isValid($form))
        {
            try
            {
                $eventService->updateSignUp($currentSignUp->getId(), $formModel);

                $this->addToast('Signup updated');

                return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
            }
            catch(EventInThePastException $e)
            {
                return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
            }
            catch(NotAuthorizedException $e)
            {
                $this->logger->warning(
                    __CLASS__ . ' currently logged in user is not allowed to remove this event',
                    [
                        "event"   => $id,
                        "account" => $account->getId()
                    ]
                );

                return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
            }
            catch(\Exception $e)
            {
                $this->logger->warning(
                    __CLASS__ . ' unexpected error',
                    [
                        "throwable" => $e,
                        "event"     => $id,
                        "account"   => $account->getId()
                    ]
                );

                return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
            }
        }
        else
        {
            return $this->render("LaDanseSiteBundle:events:editSignUp.html.twig",
                ['event' => $event, 'form' => $form->createView()]);
        }
    }
}
