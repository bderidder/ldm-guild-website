<?php

namespace LaDanse\ServicesBundle\Service\DTO\Forum;

use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class LastPostEntry
{
    /**
     * @SerializedName("postDate")
     *
     * @var \DateTime
     */
    protected $postDate;

    /**
     * @SerializedName("posterRef")
     *
     * @var AccountReference
     */
    protected $posterRef;

    /**
     * LastPostEntry constructor.
     *
     * @param \DateTime $postDate
     * @param AccountReference $posterRef
     */
    public function __construct(\DateTime $postDate,
                                AccountReference $posterRef)
    {
        $this->postDate = $postDate;
        $this->posterRef = $posterRef;
    }

    /**
     * @return \DateTime
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * @return AccountReference
     */
    public function getPosterRef()
    {
        return $this->posterRef;
    }
}