<?php

namespace LaDanse\SiteBundle\Form\Type;

use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateClaimFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('roles', ChoiceType::class, [
    				'choices'   => [
                        'Tank'   => Role::TANK,
                        'Healer' => Role::HEALER,
                        'Damage' => Role::DPS],
                    'expanded'	=> true,
    				'multiple'	=> true])
		        ->add('character', ChoiceType::class, [
                    'choices' => $options['unclaimedChars'],
                    'expanded'  => false,
                    'multiple'  => false])
                ->add('save', SubmitType::class, [
                    'label'  => 'save',
                    'attr'   =>  ['class' => 'btn-primary']]);
	}

    /**
     * @return string
     */
    public function getBlockPrefix()
	{
		return 'NewClaimForm';
	}

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'unclaimedChars' => null,
            ]
        );
    }
}