<?php


namespace LaDanse\ServicesBundle\Command\Armory;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;
use LaDanse\ServicesBundle\Common\CommandExecutionContext;

final class BattleNetUtils
{
    const GET_TIMEOUT = 60; // seconds of timeout

    /**
     * @param CommandExecutionContext $context
     * @param $battleNetKey
     * @param $battleNetSecret
     *
     * @return string|null
     * @throws Exception
     */
    public static function getBlizzardAccessToken(CommandExecutionContext $context, $battleNetKey, $battleNetSecret): ?string
    {
        $tokenEndpointUrl = "https://eu.battle.net/oauth/token";

        $client = new Client();

        try
        {
            $response = $client->request(
                'POST',
                $tokenEndpointUrl,
                [
                    'timeout' => BattleNetUtils::GET_TIMEOUT,
                    'auth' => [
                        $battleNetKey,
                        $battleNetSecret],
                    'query' =>
                        [
                            'grant_type' => 'client_credentials'
                        ]
                ]
            );

            if ($response->getStatusCode() != 200)
            {
                $context->error("Status code was not 200 but " . $response->getStatusCode());

                throw new Exception("Status code was not 200 but " . $response->getStatusCode());
            }
            else
            {
                /** @var Stream $jsonBody */
                $jsonBodyStream = $response->getBody();

                $jsonBody = $jsonBodyStream->getContents();

                $tokenResponse = json_decode($jsonBody);

                return $tokenResponse->access_token;
            }
        }
        catch(Exception $e)
        {
            $context->error("Got exception while retrieving Access Token " . $e->getMessage());

            throw $e;
        }
    }

    /**
     * @param CommandExecutionContext $context
     * @param string $accessToken
     * @param string $namespacePrefix
     * @param string $endpointUrl
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function callBattleNetAPI(CommandExecutionContext $context, string $accessToken, string $namespacePrefix, string $endpointUrl)
    {
        $client = new Client();

        try
        {
            $response = $client->request(
                'GET',
                $endpointUrl,
                [
                    'timeout' => BattleNetUtils::GET_TIMEOUT,
                    'query' =>
                        [
                            'namespace' => $namespacePrefix . '-eu',
                            'locale' => 'en_US',
                            'access_token' => $accessToken
                        ]
                ]
            );

            if ($response->getStatusCode() != 200)
            {
                $context->error("Status code was not 200 but " . $response->getStatusCode());

                throw new Exception("Status code was not 200 but " . $response->getStatusCode());
            }
            else
            {
                /** @var Stream $jsonBody */
                $jsonBodyStream = $response->getBody();

                $jsonBody = $jsonBodyStream->getContents();

                $response = json_decode($jsonBody);

                return $response;
            }
        }
        catch(Exception $e)
        {
            $context->error("Got exception while calling BattleNet API " . $e->getMessage());

            throw $e;
        }
    }

    /**
     * @param mixed $object
     * @param array $propertyNames
     *
     * @return bool
     */
    public static function verifyProperties($object, array $propertyNames)
    {
        $hasAllProperties = true;

        foreach($propertyNames as $propertyName)
        {
            $hasAllProperties = $hasAllProperties && property_exists($object, $propertyName);
        }

        return $hasAllProperties;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public static function sluggify(string $input): string
    {
        /*
         * Be careful to use mb_strtolower instead of strtolower as the latter cannot handle multibyte UTF-8 encoding
         */

        return urlencode(mb_strtolower(str_replace(' ', '-', $input)));
    }
}