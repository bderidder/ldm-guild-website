<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EventFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text')
		        ->add('description', 'textarea')
		        ->add('date', 'date')
		        ->add('inviteTime', 'time')
		        ->add('startTime', 'time')
		        ->add('endTime', 'time')
		        ->add('save', 'submit');
	}

	public function getName()
	{
		return 'NewEvent';
	}
}