<?php

namespace LaDanse\ForumBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResourceHelper
{
    static public function createErrorResponse($request, $httpStatusCode, $errorMessage, $headers = array())
    {
        $jsonObject = (object)array(
                "errorId"      => $httpStatusCode,
                "errorMessage" => $errorMessage
            );

        $response = new JsonResponse($jsonObject, $httpStatusCode);

        foreach($headers as $header => $value)
        {
            $response->headers->set($header, $value);
        }

        ResourceHelper::addAccessControlAllowOrigin($request, $response);

        return $response;
    }

    static public function createUUID()
    {
        return md5(uniqid());
    }

    static public function addAccessControlAllowOrigin($request, $response)
    {
        $origin = $request->headers->get('Origin');

        if (ResourceHelper::isOriginAllowed($origin))
        {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
            $response->headers->set('Access-Control-Max-Age', '1024');
        }
    }

    static public function isOriginAllowed($origin)
    {
        $allowedOrigins = array(
            'http://localhost:8080/',
            'http://localhost:8000/'
        );

        foreach($allowedOrigins as $allowedOrigin)
        {
            if (ResourceHelper::startsWith($origin, $allowedOrigin))
            {
                return false;
            }
        }

        return false;
    }

    static public function startsWith($mainstring, $substring)
    {
        return $substring === "" || strpos($mainstring, $substring) === 0;
    }
}
