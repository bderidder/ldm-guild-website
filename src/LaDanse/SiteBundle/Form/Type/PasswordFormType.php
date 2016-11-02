<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'passwordOne',
                PasswordType::class,
                ['label' => "New Password"]
            )
            ->add(
                'passwordTwo',
                PasswordType::class,
                ['label' => "Repeat Password"]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'change',
                    'attr'  =>  ['class' => 'btn-primary']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['show_legend' => false]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'EditPassword';
    }
}