<?php

namespace AppBundle\Form;

use AppBundle\Entity\Rating;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RatingType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('value', ChoiceType::class, [
				'label' => 'book.value.label',
				'choices' => [
					'book.value.5' => 5,
					'book.value.4' => 4,
					'book.value.3' => 3,
					'book.value.2' => 2,
					'book.value.1' => 1
				]
			])
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Rating::class,
		]);
	}


}