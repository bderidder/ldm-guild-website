<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                ['label' => "Username"]
            )
            ->add(
                'displayName',
                TextType::class,
                ['label' => "Display Name"]
            )
            ->add(
                'email',
                TextType::class,
                ['label' => "Email"]
            )
            ->add(
                'passwordOne',
                PasswordType::class,
                ['label' => "Password"]
            )
            ->add(
                'passwordTwo',
                PasswordType::class,
                ['label' => "Repeat Password"]
            )
            ->add(
                'save', SubmitType::class,
                [
                    'label' => 'register',
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
        return 'RegisterProfile';
    }
}