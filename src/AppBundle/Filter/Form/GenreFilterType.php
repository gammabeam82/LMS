<?php

namespace AppBundle\Filter\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class GenreFilterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'genre.genre',
				'required' => false
			])
			->add('sortByName', CheckboxType::class, [
				'label' => 'messages.sort_by_name',
				'required' => false
			]);
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Filter\GenreFilter',
			'csrf_protection' => false,
			'method' => 'GET'
		]);
	}


}