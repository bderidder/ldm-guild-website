<?php

namespace LaDanse\SiteBundle\Controller\TeamSpeak;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TSProxyController extends LaDanseController
{
    /**
     * @return Response
     *
     * @Route("/clients")
     */
    public function clientsAction()
    {
        $clientsJson = file_get_contents('http://ts.ladanse.org/TeamSpeak/rest/v1/clients');

        $clients = json_decode($clientsJson);

        return new JsonResponse($clients);
    }
}
