<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SignUpFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('roles', 'choice', array(
    				'choices'   => array('t' => 'Tank', 'h' => 'Healer', 'd' => 'DPS'),
    				'required'  => true,
    				'expanded'	=> true,
    				'multiple'	=> true
				))
		        ->add('type', 'choice', array(
    				'choices'   => array('1' => 'Will come', '2' => 'Might come'),
    				'required'  => true,
    				'expanded'	=> true,
    				'multiple'	=> false
				))
		        ->add('save', 'submit');
	}

	public function getName()
	{
		return 'SignUpForm';
	}
}