<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('passwordOne', 'password', array('label' => "New Password"))
                ->add('passwordTwo', 'password', array('label' => "Repeat Password"))
                ->add('save', 'submit', array(
                    'label'  => 'Save',
                    'attr'   =>  array(
                        'class'   => 'btn-primary')
            ));
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
        return 'EditPassword';
    }
}