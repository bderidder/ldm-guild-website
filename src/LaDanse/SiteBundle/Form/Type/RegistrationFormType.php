<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => "Username"))
                ->add('displayName', 'text', array('label' => "Display Name"))
                ->add('email', 'text', array('label' => "Email"))
                ->add('passwordOne', 'password', array('label' => "Password"))
                ->add('passwordTwo', 'password', array('label' => "Repeat Password"))
                ->add('save', 'submit', array(
                    'label'  => 'register',
                    'attr'   =>  array(
                        'class'   => 'btn-primary')
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('show_legend' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'RegisterProfile';
    }
}