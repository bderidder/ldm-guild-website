<?php

namespace LaDanse\ServicesBundle\Service\DTO\Topic;

use JMS\Serializer\Annotation\SerializedName;

class Post
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    protected $message;
}