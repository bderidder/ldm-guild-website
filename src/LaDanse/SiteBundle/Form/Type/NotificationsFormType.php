<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificationsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('newEvents', 'checkbox', array('label' => "New event is created"))
                ->add('changeSignedEvent', 'checkbox', array('label' => "An event I signed up for changed"))
                ->add('signUpChange', 'checkbox', array('label' => "Someone signed up for an event I created"))
                ->add('change', 'submit', array(
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
        return 'EditNotifications';
    }
}