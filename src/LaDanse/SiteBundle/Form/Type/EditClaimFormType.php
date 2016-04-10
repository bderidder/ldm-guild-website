<?php

namespace LaDanse\SiteBundle\Form\Type;

use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class EditClaimFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('roles', ChoiceType::class, array(
    				'choices'   => array(
    					Role::TANK   => 'Tank',
    					Role::HEALER => 'Healer',
    					Role::DPS    => 'Damage'),
    				'expanded'	=> true,
    				'multiple'	=> true
				))
            ->add('save', SubmitType::class, array(
                'label'  => 'save',
                'attr'   =>  array(
                    'class'   => 'btn-primary')
            ));
	}

    /**
     * @return string
     */
    public function getBlockPrefix()
	{
		return 'EditClaimForm';
	}
}