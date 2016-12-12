<?php

namespace AppBundle\Filter\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class BookFilterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'book.book',
				'required' => false
			])
			->add('author', EntityType::class, [
				'class' => 'AppBundle:Author',
				'label' => 'book.author',
				'choice_label' => 'shortName',
				'multiple' => true,
				'required' => false
			])
			->add('genre', EntityType::class, [
				'class' => 'AppBundle:Genre',
				'label' => 'book.genre',
				'choice_label' => 'name',
				'multiple' => true,
				'required' => false
			])
			->add('search', HiddenType::class, [])
			->add('createdAtStart', DateType::class, [
				'label' => 'messages.added_from',
				'widget' => 'single_text',
				'format' => 'dd.MM.yyyy',
				'attr' => [
					'class' => 'form-control datepicker',
					'data-provide' => 'datepicker',
					'format' => 'DD.MM.YYY'
				],
				'required' => false,
			])
			->add('createdAtEnd', DateType::class, [
				'label' => 'messages.added_to',
				'widget' => 'single_text',
				'format' => 'dd.MM.yyyy',
				'attr' => [
					'class' => 'form-control datepicker',
					'data-provide' => 'datepicker',
					'format' => 'DD.MM.YYY'
				],
				'required' => false,
			])
			->add('mostPopular', CheckboxType::class, [
				'label' => 'messages.popular',
				'required' => false
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Filter\BookFilter',
			'csrf_protection' => false,
			'method' => 'GET'
		]);
	}


}