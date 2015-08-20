<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificationsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('newEvents', 'checkbox', array('label' => "Change this in template"))
                ->add('changeSignedEvent', 'checkbox', array('label' => "Change this in template"))
                ->add('signUpChange', 'checkbox', array('label' => "Change this in template"))
                ->add('topicCreated', 'checkbox', array('label' => "Change this in template"))
                ->add('replyToTopic', 'checkbox', array('label' => "Change this in template"))
                ->add('allForumPosts', 'checkbox', array('label' => "Change this in template"))
                ->add('change', 'submit', array(
                    'label'  => 'save',
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