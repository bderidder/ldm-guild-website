<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => "Login"))
                ->add('displayName', 'text', array('label' => "Display Name"))
                ->add('email', 'text', array('label' => "Email"))
                ->add('passwordOne', 'password', array('label' => "Password"))
                ->add('passwordTwo', 'password', array('label' => "Repeat Password"))
                ->add('save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
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