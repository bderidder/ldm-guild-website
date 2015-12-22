<?php

namespace LaDanse\ServicesBundle\Service\DTO\Account;

use JMS\Serializer\Annotation\SerializedName;

class PatchAccount
{
    /**
     * @SerializedName("username")
     *
     * @var string
     */
    protected $username;

    /**
     * @SerializedName("displayName")
     *
     * @var string
     */
    protected $displayName;

    /**
     * @SerializedName("email")
     *
     * @var string
     */
    protected $email;

    /**
     * @SerializedName("enabled")
     *
     * @var boolean
     */
    protected $enabled;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}