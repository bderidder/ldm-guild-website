<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login', 'text', array('label' => "Login", 'read_only' => true))
                ->add('displayName', 'text', array('label' => "Display Name"))
                ->add('email', 'text', array('label' => "Email"))
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
        return 'EditProfile';
    }
}