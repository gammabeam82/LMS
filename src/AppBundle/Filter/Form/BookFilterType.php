<?php

namespace AppBundle\Filter\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Serie;
use AppBundle\Filter\DTO\BookFilter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityRepository;

class BookFilterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'messages.name',
				'required' => false
			])
			->add('author', EntityType::class, [
				'class' => Author::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->orderBy('a.lastName', 'ASC');
				},
				'label' => 'book.author',
				'choice_label' => 'shortName',
				'multiple' => true,
				'required' => false
			])
			->add('genre', EntityType::class, [
				'class' => Genre::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('g')
						->orderBy('g.name', 'ASC');
				},
				'label' => 'book.genre',
				'choice_label' => 'name',
				'multiple' => true,
				'required' => false
			])
			->add('serie', EntityType::class, [
				'class' => Serie::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('s')
						->orderBy('s.name', 'ASC');
				},
				'label' => 'book.serie',
				'choice_label' => 'name',
				'multiple' => true,
				'required' => false
			])
			->add('search', HiddenType::class, [])
			->add('createdAtStart', DateType::class, [
				'label' => 'messages.created_at',
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
				'label' => '',
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
			->add('liked', CheckboxType::class, [
				'label' => 'messages.favorites',
				'required' => false
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => BookFilter::class,
			'csrf_protection' => false,
			'method' => 'GET'
		]);
	}


}