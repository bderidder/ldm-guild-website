<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Common;

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
    public static function createErrorResponse($request, $httpStatusCode, $errorMessage, $headers = [])
    {
        $errorResponse = new ErrorResponse();
        $errorResponse
            ->setErrorCode($httpStatusCode)
            ->setErrorMessage($errorMessage);

        $response = new JsonResponse($errorResponse, $httpStatusCode);

        foreach ($headers as $header => $value)
        {
            $response->headers->set($header, $value);
        }

        ResourceHelper::addAccessControlAllowOrigin($request, $response);

        return $response;
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
        $allowedOrigins = [
            'http://localhost:8080/',
            'http://localhost:8000/'
        ];

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

    static public function object($object)
    {
        if ($object != null)
        {
            return $object;
        }
        else
        {
            return (object)[];
        }
    }

    static public function array($array)
    {
        if ($array != null)
        {
            return $array;
        }
        else
        {
            return [];
        }
    }
}
