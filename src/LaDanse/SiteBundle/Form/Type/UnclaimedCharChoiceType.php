<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Validator\Constraints;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;

class UnclaimedCharChoiceType extends AbstractType
{
	private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getDefaultOptions()
    {
        return array(
            'choices' => array(
                '1' => 'Test 1',
                '2' => 'Test 2'),
            'expanded'  => false,
            'multiple'  => false
        );
    }

    public function getParent()
    {
        return "choice";
    }

    public function getName()
    {
        return "unclaimed_char_choice";
    }
}