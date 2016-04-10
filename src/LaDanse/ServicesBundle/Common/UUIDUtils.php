<?php

namespace LaDanse\ServicesBundle\Common;

class UUIDUtils
{
    /**
     * @return string
     */
    public static function createUUID()
    {
        return md5(uniqid());
    }
}