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
class IntegerReference
{
    /**
     * @var int
     * @Type("int")
     * @SerializedName("id")
     */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return (string)$this->id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return IntegerReference
     */
    public function setId(int $id): IntegerReference
    {
        $this->id = $id;
        return $this;
    }
}