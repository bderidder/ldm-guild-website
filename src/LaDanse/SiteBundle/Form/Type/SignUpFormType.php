<?php

namespace LaDanse\SiteBundle\Form\Type;

use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class SignUpFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('roles', 'choice', array(
    				'choices'   => array(
    					Role::TANK   => 'Tank',
    					Role::HEALER => 'Healer',
    					Role::DPS    => 'Damage'),
    				'expanded'	=> true,
    				'multiple'	=> true
				))
		        ->add('type', 'choice', array(
    				'choices'   => array(
    					SignUpType::WILLCOME  => 'Will come',
    					SignUpType::MIGHTCOME => 'Might come',
						SignUpType::ABSENCE => 'Can\'t come'),
    				'expanded'	=> true,
    				'multiple'	=> false
				))
                ->add('save', 'submit', array(
                    'label'  => 'save',
                    'attr'   =>  array(
                        'class'   => 'btn-primary')
                ));
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'SignUpForm';
	}
}