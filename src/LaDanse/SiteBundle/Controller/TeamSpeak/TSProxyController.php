<?php

namespace LaDanse\SiteBundle\Controller\TeamSpeak;

use \DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\DomainBundle\Entity\Event;

use LaDanse\SiteBundle\Security\AuthenticationContext;
use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;

use LaDanse\SiteBundle\Model\ErrorModel;

class TSProxyController extends LaDanseController
{
    /**
     * @Route("/clients")
     */
    public function clientsAction(Request $request)
    {
        $clientsJson = file_get_contents('http://ts.ladanse.org/TeamSpeak/rest/v1/clients');

        $clients = json_decode($clientsJson);

        return new JsonResponse($clients);
    }
}
