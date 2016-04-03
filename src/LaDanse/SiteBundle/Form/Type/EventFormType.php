<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', TextType::class, array(
                	'label' => "Name"
                	))
		        ->add('description', TextareaType::class)
		        ->add('date', 'date', array(
		        	'widget' => 'single_text',
		        	'format' => 'EEE dd-MM-yyyy',
					'attr' => array('class' => 'date')))
		        ->add('inviteTime', TimeType::class, array(
		        	'widget' => 'single_text'))
		        ->add('startTime', TimeType::class, array(
		        	'widget' => 'single_text'))
		        ->add('endTime', TimeType::class, array(
		        	'widget' => 'single_text'))
                ->add('save', SubmitType::class, array(
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
		return 'NewEvent';
	}
}