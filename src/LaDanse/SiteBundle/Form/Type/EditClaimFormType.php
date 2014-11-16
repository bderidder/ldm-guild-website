<?php

namespace LaDanse\SiteBundle\Form\Type;

use LaDanse\CommonBundle\Helper\ContainerInjector;
use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class EditClaimFormType extends AbstractType
{
    protected $container;

    public function __construct(ContainerInjector $injector)
    {
        $this->container = $injector->getContainer();
    }

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('roles', 'choice', array(
    				'choices'   => array(
    					Role::TANK   => 'Tank',
    					Role::HEALER => 'Healer',
    					Role::DPS    => 'Damage'),
    				'expanded'	=> true,
    				'multiple'	=> true
				))
            ->add('save', 'submit', array(
                'label'  => 'save',
                'attr'   =>  array(
                    'class'   => 'btn-primary')
            ));
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'EditClaimForm';
	}
}