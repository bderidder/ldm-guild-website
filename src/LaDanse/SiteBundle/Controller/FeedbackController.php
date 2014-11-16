<?php

namespace LaDanse\SiteBundle\Controller;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Form\Model\FeedbackFormModel;
use LaDanse\SiteBundle\Form\Type\FeedbackFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/feedback")
*/
class FeedbackController extends LaDanseController
{
    /**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/create", name="createFeedbackIndex")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $formModel = new FeedbackFormModel();
        $formModel->setDescription('');

        $form = $this->createForm(new FeedbackFormType(), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
           $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid())
            {
                $this->sendFeedback($authContext->getAccount(), $formModel->getDescription());

                return $this->render('LaDanseSiteBundle:feedback:feedbackResult.html.twig');
            }
            else
            {
                return $this->render('LaDanseSiteBundle:feedback:feedbackForm.html.twig',
                        array('form' => $form->createView(), 'errors' => $errors));
            }
        }
        else
        {
            return $this->render('LaDanseSiteBundle:feedback:feedbackForm.html.twig',
                        array('form' => $form->createView()));
        }
    }

    private function sendFeedback($account, $description)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Feedback from La Danse site')
            ->setFrom('noreply@ladanse.org')
            ->setTo('bderidder@gmail.com')
            ->addPart($this->renderView(
                    'LaDanseSiteBundle:feedback:email.txt.twig',
                    array('description' => $description, 'account' => $account)
                ), 'text/plain; charset=utf-8')
            ->addPart($this->renderView(
                    'LaDanseSiteBundle:feedback:email.html.twig',
                    array('description' => $description, 'account' => $account)
                ), 'text/html; charset=utf-8');
    
        $this->get('mailer')->send($message);
    }
}
