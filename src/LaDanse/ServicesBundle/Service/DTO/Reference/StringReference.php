<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExclusionPolicy("none")
 */
class StringReference
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("id")
     * @Assert\NotBlank()
     */
    private $id;

    public function __construct(string $id = null)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return StringReference
     */
    public function setId(string $id): StringReference
    {
        $this->id = $id;
        return $this;
    }
}