<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;

class PatchSignUp
{
    /**
     * @SerializedName("type")
     *
     * @var string
     */
    protected $type;

    /**
     * @SerializedName("roles")
     *
     * @var array
     */
    protected $roles;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}
