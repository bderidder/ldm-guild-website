<?php

namespace LaDanse\SiteBundle\Form\Model;

class SettingsFormModel
{
	private $createEventMail = FALSE;
    private $changeEventMail = FALSE;
    private $cancelEventMail = FALSE;

    /**
     * @param bool $createEventMail
     * @return SettingsFormModel
     */
    public function setCreateEventMail($createEventMail)
    {
        $this->createEventMail = $createEventMail;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCreateEventMail()
    {
        return $this->createEventMail;
    }

    /**
     * @param bool $changeEventMail
     * @return SettingsFormModel
     */
    public function setChangeEventMail($changeEventMail)
    {
        $this->changeEventMail = $changeEventMail;

        return $this;
    }

    /**
     * @return bool
     */
    public function getChangeEventMail()
    {
        return $this->createEventMail;
    }

    /**
     * @param bool $cancelEventMail
     * @return SettingsFormModel
     */
    public function setCancelEventMail($cancelEventMail)
    {
        $this->cancelEventMail = $cancelEventMail;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCancelEventMail()
    {
        return $this->cancelEventMail;
    }
}