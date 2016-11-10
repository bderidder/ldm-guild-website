<?php

namespace LaDanse\SiteBundle\Controller\TeamSpeak;

use GuzzleHttp\Client;
use LaDanse\SiteBundle\Common\LaDanseController;
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
        return $this->getJsonFromUrl('https://ts.ladanse.org/TeamSpeak/rest/v1/clients');
    }

    /**
     * @return Response
     *
     * @Route("/channels")
     */
    public function channelsAction()
    {
        return $this->getJsonFromUrl('https://ts.ladanse.org/TeamSpeak/rest/v1/channels');
    }

    private function getJsonFromUrl($url)
    {
        $client = new Client();

        try
        {
            $response = $client->request('GET', $url, ['timeout' => 2]); // time out of 2 seconds

            if ($response->getStatusCode() != 200)
            {
                return new Response("Error " . $response->getStatusCode(), 500);
            }
            else
            {
                $json = json_decode($response->getBody());

                return new JsonResponse($json);
            }
        }
        catch(\Exception $e)
        {
            return new Response("Error " . $e->getMessage(), 500);
        }
    }
}
