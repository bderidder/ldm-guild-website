<?php

namespace LaDanse\SiteBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class FeedbackFormModel
{
    private $description;

    /**
     * Set description
     *
     * @param string $description
     * @return EventFormModel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @Assert\NotBlank()
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}