<?php

namespace LaDanse\SiteBundle\Form\Type;

use Symfony\Component\Validator\Constraints;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use LaDanse\CommonBundle\Helper\ContainerInjector;

use LaDanse\DomainBundle\Entity\Role;

use LaDanse\ServicesBundle\Service\GuildCharacterService;

class NewClaimFormType extends AbstractType
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
		        ->add('character', 'choice', array(
                    'choices' => $this->getUnclaimedChoices(),
                    'expanded'  => false,
                    'multiple'  => false))
                ->add('save', 'submit');
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'NewClaimForm';
	}

    private function getUnclaimedChoices()
    {
        $unclaimedChars = $this->container->get(GuildCharacterService::SERVICE_NAME)->getUnclaimedCharacters();

        $choices = array();

        foreach($unclaimedChars as $unclaimedChar)
        {
            $choices[$unclaimedChar->id] = $unclaimedChar->name;
        }

        return $choices;
    }
}