<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Form\Model\CalExportFormModel;
use LaDanse\SiteBundle\Form\Type\CalExportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\CalendarExport;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class EditCalExportController extends LaDanseController
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
     *
     * @return Response
     *
     * @Route("/calExport", name="editCalExport")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in editProfile');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $account = $authContext->getAccount();

        $calExport = $this->getExportSettings($account);

        $formModel = new CalExportFormModel();

        $formModel->setExportSignUp(true);
        $formModel->setExportNew($calExport->getExportNew());
        $formModel->setExportAbsence($calExport->getExportAbsence());

        $form = $this->createForm(new CalExportFormType(), $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $this->updateExportSettings($calExport, $formModel);

                $this->eventDispatcher->dispatch(
                    ActivityEvent::EVENT_NAME,
                    new ActivityEvent(
                        ActivityType::SETTINGS_CALEXPORT_UPDATE,
                        $this->getAuthenticationService()->getCurrentContext()->getAccount())
                );

               return $this->redirect($this->generateUrl('editCalExport'));
            }
            else
            {
                return $this->render('LaDanseSiteBundle:settings:editCalExport.html.twig',
                    array(
                        'form' => $form->createView(),
                        'personalUrl' => $this->generateUrl('icalIndex', array('secret' => $calExport->getSecret()), true)
                    )
                );
            }
        }
        else
        {
            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::SETTINGS_CALEXPORT_VIEW,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount())
            );

            return $this->render('LaDanseSiteBundle:settings:editCalExport.html.twig',
                array(
                    'form' => $form->createView(),
                    'personalUrl' => $this->generateUrl('icalIndex', array('secret' => $calExport->getSecret()), true)
                )
            );
        }
    }

    /**
     * @return Response
     *
     * @Route("/resetSecret", name="resetSecret")
     */
    public function resetSecret()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in editProfile');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $account = $authContext->getAccount();

        $calExport = $this->getExportSettings($account);

        $calExport->setSecret($this->generateRandomString(25));

        $em = $this->getDoctrine()->getManager();
        $em->persist($calExport);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SETTINGS_CALEXPORT_RESET,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->redirect($this->generateUrl('editCalExport'));
    }

    protected function getExportSettings(Account $account)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from('LaDanse\DomainBundle\Entity\CalendarExport', 's')
            ->where($qb->expr()->eq('s.account', '?1'))
            ->setParameter(1, $account);

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving CalendarExport ",
            array(
                "query" => $qb->getDQL()
            )
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $result = $query->getResult();

        if (count($result) != 1)
        {
            $calExport = $this->createExportSettings($account);

            $em->persist($calExport);
            $em->flush();

            return $calExport;
        }
        else
        {
            return $result[0];
        }
    }

    protected function createExportSettings(Account $account)
    {
        $calExport = new CalendarExport();

        $calExport->setAccount($account);
        $calExport->setExportAbsence(true);
        $calExport->setExportNew(true);
        $calExport->setSecret($this->generateRandomString(25));

        return $calExport;
    }

    protected function updateExportSettings(CalendarExport $calExport, CalExportFormModel $formModel)
    {
        $em = $this->getDoctrine()->getManager();

        $calExport->setExportAbsence($formModel->isExportAbsence());
        $calExport->setExportNew($formModel->isExportNew());

        $em->persist($calExport);
        $em->flush();
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
