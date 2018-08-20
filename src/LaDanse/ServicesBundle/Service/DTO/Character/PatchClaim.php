<?php
/**
 * Created by PhpStorm.
 * User: bavo
 * Date: 21/08/16
 * Time: 15:03
 */

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExclusionPolicy("none")
 */
class PatchClaim
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("comment")
     */
    private $comment;

    /**
     * @var bool
     * @Type("boolean")
     * @SerializedName("raider")
     */
    private $raider;

    /**
     * @var array
     * @Type("array<string>")
     * @SerializedName("roles")
     */
    private $roles = [];

    /**
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return PatchClaim
     */
    public function setComment($comment): PatchClaim
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRaider(): bool
    {
        return $this->raider;
    }

    /**
     * @param boolean $raider
     * @return PatchClaim
     */
    public function setRaider(bool $raider): PatchClaim
    {
        $this->raider = $raider;
        return $this;
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
     * @return PatchClaim
     */
    public function setRoles(array $roles): PatchClaim
    {
        $this->roles = $roles;
        return $this;
    }
}