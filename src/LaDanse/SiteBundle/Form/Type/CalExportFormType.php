<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalExportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('exportSignUp', 'checkbox', array('label' => "dummy label", 'disabled' => true))
                ->add('exportAbsence', 'checkbox', array('label' => "dummy label"))
                ->add('exportNew', 'checkbox', array('label' => "dummy label"))
                ->add('change', 'submit', array(
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
        return 'EditCalExport';
    }
}