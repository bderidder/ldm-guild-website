<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

use JMS\Serializer\Annotation\SerializedName;

class CommentGroupReference
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    /**
     * AccountReference constructor.
     *
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}