<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\DTO\Callback;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExclusionPolicy("none")
 */
class LogCallback
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("source")
     * @Assert\NotBlank()
     */
    protected $source;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("message")
     * @Assert\NotBlank()
     */
    protected $message;

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return LogCallback
     */
    public function setSource(string $source): LogCallback
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return LogCallback
     */
    public function setMessage(string $message): LogCallback
    {
        $this->message = $message;
        return $this;
    }
}