<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalExportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('exportSignUp', CheckboxType::class, ['label' => "dummy label", 'disabled' => true])
                ->add('exportAbsence', CheckboxType::class, ['label' => "dummy label"])
                ->add('exportNew', CheckboxType::class, ['label' => "dummy label"])
                ->add('change', SubmitType::class, [
                    'label'  => 'save',
                    'attr'   =>  [
                        'class'   => 'btn-primary']
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
        return 'EditCalExport';
    }
}