<?php

namespace AppBundle\Filter\Form;

use AppBundle\Filter\AuthorFilter;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AuthorFilterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('lastName', TextType::class, [
				'label' => 'author.author',
				'required' => false
			])
			->add('firstLetter', HiddenType::class, [
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
			'data_class' => AuthorFilter::class,
			'csrf_protection' => false,
			'method' => 'GET'
		]);
	}


}