<?php

namespace LaDanse\ServicesBundle\Service\DTO\Forum;

use JMS\Serializer\Annotation\SerializedName;

class Forum
{
    /**
     * @SerializedName("id")
     *
     * @var string
     */
    protected $id;

    /**
     * @SerializedName("name")
     *
     * @var string
     */
    protected $name;

    /**
     * @SerializedName("description")
     *
     * @var string
     */
    protected $description;

    /**
     * @SerializedName("topics")
     *
     * @var array
     */
    protected $topicEntries;

    /**
     * Forum constructor.
     * @param string $id
     * @param string $name
     * @param string $description
     * @param array $topicEntries
     */
    public function __construct($id,
                                $name,
                                $description,
                                array $topicEntries = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->topicEntries = $topicEntries;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getTopicEntries()
    {
        return $this->topicEntries;
    }
}