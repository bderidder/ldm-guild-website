<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ResourceHelper
 * @package LaDanse\ForumBundle\Controller
 */
class ResourceHelper
{
    /**
     * @param $request
     * @param $httpStatusCode
     * @param $errorMessage
     * @param array $headers
     * @return JsonResponse
     */
    public static function createErrorResponse($request, $httpStatusCode, $errorMessage, $headers = array())
    {
        $jsonObject = (object)array(
                "errorId"      => $httpStatusCode,
                "errorMessage" => $errorMessage
            );

        $response = new JsonResponse($jsonObject, $httpStatusCode);

        foreach ($headers as $header => $value)
        {
            $response->headers->set($header, $value);
        }

        ResourceHelper::addAccessControlAllowOrigin($request, $response);

        return $response;
    }

    /**
     * @return string
     */
    public static function createUUID()
    {
        return md5(uniqid());
    }

    /**
     * @param $request
     * @param $response
     */
    public static function addAccessControlAllowOrigin($request, $response)
    {
        $origin = $request->headers->get('Origin');

        if (ResourceHelper::isOriginAllowed($origin))
        {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
            $response->headers->set('Access-Control-Max-Age', '1024');
        }
    }

    /**
     * @param $origin
     * @return bool
     */
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

    /**
     * @param $mainstring
     * @param $substring
     * @return bool
     */
    static public function startsWith($mainstring, $substring)
    {
        return $substring === "" || strpos($mainstring, $substring) === 0;
    }
}
