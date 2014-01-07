<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingsFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('createEventMail', 'checkbox', array(
    			      'label'                 => 'When a new event is created',
    			      'widget_checkbox_label' => 'widget',
    			      'required'              => FALSE))
		        ->add('changeEventMail', 'checkbox', array(
    			      'label'                 => 'When an event I signed up for changed (date, time or description)',
    			      'widget_checkbox_label' => 'widget',
    			      'required'              => FALSE))
		        ->add('cancelEventMail', 'checkbox', array(
    			      'label'                 => 'When an event I signed up for is cancelled',
    			      'widget_checkbox_label' => 'widget',
    			      'required'              => FALSE))
		        ->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('show_legend' => false));
    }

	public function getName()
	{
		return 'EditSettings';
	}
}