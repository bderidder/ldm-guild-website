<?php

namespace LaDanse\ServicesBundle\Service\Discord;


class DiscordRedirectValidator
{
    public static function validate($url)
    {
        $urlParts = parse_url($url);

        if (!array_key_exists('host', $urlParts))
        {
            return false;
        }

        $hostPart = $urlParts['host'];

        return $hostPart == 'localhost' || DiscordRedirectValidator::endsWith($hostPart, ".ladanse.org");
    }

    private static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }
}