<?php

namespace LaDanse\SiteBundle\Form\Model;

class SettingsFormModel
{
	private $createEventMail = FALSE;
    private $changeEventMail = FALSE;
    private $cancelEventMail = FALSE;

    public function setCreateEventMail($createEventMail)
    {
        $this->createEventMail = $createEventMail;

        return $this;
    }

    public function getCreateEventMail()
    {
        return $this->createEventMail;
    }

    public function setChangeEventMail($changeEventMail)
    {
        $this->changeEventMail = $changeEventMail;

        return $this;
    }

    public function getChangeEventMail()
    {
        return $this->createEventMail;
    }

    public function setCancelEventMail($cancelEventMail)
    {
        $this->cancelEventMail = $cancelEventMail;

        return $this;
    }

    public function getCancelEventMail()
    {
        return $this->cancelEventMail;
    }
}