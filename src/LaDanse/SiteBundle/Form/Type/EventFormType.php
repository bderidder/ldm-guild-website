<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', array(
                	'label' => "Name"
                	))
		        ->add('description', 'textarea')
		        ->add('date', 'date', array(
		        	'widget' => 'single_text',
		        	'format' => 'EEE dd-MM-yyyy',
					'attr' => array('class' => 'date')))
		        ->add('inviteTime', 'time', array(
		        	'widget' => 'single_text'))
		        ->add('startTime', 'time', array(
		        	'widget' => 'single_text'))
		        ->add('endTime', 'time', array(
		        	'widget' => 'single_text'))
                ->add('save', 'submit', array(
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
    public function getName()
	{
		return 'NewEvent';
	}
}