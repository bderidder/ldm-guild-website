<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('newEvents', CheckboxType::class, array('label' => "Change this in template"))
                ->add('changeSignedEvent', CheckboxType::class, array('label' => "Change this in template"))
                ->add('signUpChange', CheckboxType::class, array('label' => "Change this in template"))
                ->add('topicCreated', CheckboxType::class, array('label' => "Change this in template"))
                ->add('replyToTopic', CheckboxType::class, array('label' => "Change this in template"))
                ->add('allForumPosts', CheckboxType::class, array('label' => "Change this in template"))
                ->add('change', SubmitType::class, array(
                    'label'  => 'save',
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
    public function getBlockPrefix()
    {
        return 'EditNotifications';
    }
}