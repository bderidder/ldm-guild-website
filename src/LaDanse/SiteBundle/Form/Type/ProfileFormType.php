<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'login',
                TextType::class,
                [
                    'label' => "Login",
                    'attr'  =>  ['readonly' => true]
                ]
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
                'save',
                SubmitType::class,
                [
                    'label' => 'save',
                    'attr' =>  ['class' => 'btn-primary']
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
        return 'EditProfile';
    }
}