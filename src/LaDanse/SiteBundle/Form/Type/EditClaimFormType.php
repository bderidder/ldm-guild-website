<?php

namespace LaDanse\SiteBundle\Form\Type;

use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditClaimFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add(
                'roles', ChoiceType::class, [
                'choices' => [
                    'Tank'   => Role::TANK,
                    'Healer' => Role::HEALER,
                    'Damage' => Role::DPS],
                'expanded'	=> true,
                'multiple'	=> true])
            ->add(
                'save', SubmitType::class, [
                'label'  => 'save',
                'attr'   =>  ['class'   => 'btn-primary']]);
	}

    /**
     * @return string
     */
    public function getBlockPrefix()
	{
		return 'EditClaimForm';
	}
}