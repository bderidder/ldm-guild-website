<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', array(
                	'label' => "Name"
                	))
		        ->add('description', 'textarea')
		        ->add('date', 'date', array(
		        	'widget' => 'single_text'))
		        ->add('inviteTime', 'time', array(
		        	'widget' => 'single_text'))
		        ->add('startTime', 'time', array(
		        	'widget' => 'single_text'))
		        ->add('endTime', 'time', array(
		        	'widget' => 'single_text'))
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
		return 'NewEvent';
	}
}