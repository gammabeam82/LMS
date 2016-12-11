<?php

namespace AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RatingType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('value', ChoiceType::class, [
				'label' => 'Оценка',
				'choices' => [
					'Отлично' => '5',
					'Хорошо' => '4',
					'Нормально' => '3',
					'Плохо' => '2',
					'Ужасно' => '1'
				]
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Rating',
		]);
	}


}