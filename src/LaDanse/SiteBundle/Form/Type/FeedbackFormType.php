<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => false,
			        'attr'  => ['rows' => '15']
                ]
            )
            ->add(
                'post feedback',
                SubmitType::class,
                [
                    'label' => 'post feedback',
                    'attr'  =>  ['class' => 'btn-primary']
                ]
            );
	}

	public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['show_legend' => false]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
	{
		return 'CreateFeedback';
	}
}