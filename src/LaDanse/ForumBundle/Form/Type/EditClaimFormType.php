<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Validator\Constraints;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Role;

use LaDanse\ServicesBundle\Service\GuildCharacterService;

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
                ->add('save', 'submit');
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'EditClaimForm';
	}
}