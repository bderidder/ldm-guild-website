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
        return $this->getJsonFromUrl('http://ts.ladanse.org/TeamSpeak/rest/v1/clients');
    }

    /**
     * @return Response
     *
     * @Route("/channels")
     */
    public function channelsAction()
    {
        return $this->getJsonFromUrl('http://ts.ladanse.org/TeamSpeak/rest/v1/channels');
    }

    private function getJsonFromUrl($url)
    {
        $curl = new \Curl\Curl();

        curl_setopt($curl->curl, CURLOPT_TIMEOUT_MS, 1000);

        $curl->get($url);

        if ($curl->error)
        {
            $errorCode = $curl->error_code;

            $curl->close();

            return new Response("Error " . $errorCode, 500);
        }
        else
        {
            $jsonResponse = $curl->response;
            $curl->close();

            $json = json_decode($jsonResponse);

            return new JsonResponse($json);
        }
    }
}
