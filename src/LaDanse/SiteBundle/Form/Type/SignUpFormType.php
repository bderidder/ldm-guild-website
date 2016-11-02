<?php

namespace LaDanse\SiteBundle\Form\Type;

use LaDanse\DomainBundle\Entity\Role;
use LaDanse\DomainBundle\Entity\SignUpType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SignUpFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add(
                'roles',
                ChoiceType::class,
                [
    				'choices'   => [
    					'Tank'   => Role::TANK,
    					'Healer' => Role::HEALER,
    					'Damage' => Role::DPS
                    ],
                    'expanded'	=> true,
    				'multiple'	=> true
                ]
            )
		    ->add(
		        'type',
                ChoiceType::class,
                [
    				'choices'   => [
                        'Will come'   => SignUpType::WILLCOME,
                        'Might come'  => SignUpType::MIGHTCOME,
                        'Can\'t come' => SignUpType::ABSENCE
                    ],
                    'expanded'	=> true,
    				'multiple'	=> false
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label'  => 'save',
                    'attr'   =>  ['class' => 'btn-primary']
                ]
            );
	}

    /**
     * @return string
     */
    public function getBlockPrefix()
	{
		return 'SignUpForm';
	}
}