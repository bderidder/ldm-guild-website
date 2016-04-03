<?php

namespace LaDanse\SiteBundle\Form\Type;

use LaDanse\CommonBundle\Helper\ContainerInjector;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class CreateClaimFormType extends AbstractType
{
    protected $container;

    public function __construct(ContainerInjector $injector)
    {
        $this->container = $injector->getContainer();
    }

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('roles', ChoiceType::class, array(
    				'choices'   => array(
    					Role::TANK   => 'Tank',
    					Role::HEALER => 'Healer',
    					Role::DPS    => 'Damage'),
    				'expanded'	=> true,
    				'multiple'	=> true
				))
		        ->add('character', ChoiceType::class, array(
                    'choices' => $this->getUnclaimedChoices(),
                    'expanded'  => false,
                    'multiple'  => false))
                ->add('save', SubmitType::class, array(
                    'label'  => 'save',
                    'attr'   =>  array(
                        'class'   => 'btn-primary')
                ));
	}

    /**
     * @return string
     */
    public function getBlockPrefix()
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