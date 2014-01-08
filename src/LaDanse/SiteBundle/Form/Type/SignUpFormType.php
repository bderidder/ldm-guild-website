<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;

class SignUpFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('roles', 'choice', array(
    				'choices'   => array(
    					Role::TANK   => 'Tank',
    					Role::HEALER => 'Healer',
    					Role::DPS    => 'DPS'),
    				'required'  => true,
    				'expanded'	=> true,
    				'multiple'	=> true
				))
		        ->add('type', 'choice', array(
    				'choices'   => array(
    					SignUpType::WILLCOME  => 'Will come',
    					SignUpType::MIGHTCOME => 'Might come'),
    				'required'  => true,
    				'expanded'	=> true,
    				'multiple'	=> false
				))
		        ->add('save', 'submit');
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'SignUpForm';
	}
}