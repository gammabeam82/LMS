<?php

namespace AppBundle\Filter\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AuthorFilterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('lastName', TextType::class, [
				'label' => 'Автор',
				'required' => false
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Filter\AuthorFilter',
			'csrf_protection' => false,
			'method' => 'GET'
		]);
	}


}