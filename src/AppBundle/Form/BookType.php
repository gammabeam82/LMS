<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Serie;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityRepository;

class BookType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'book.name'
			])
			->add('author', EntityType::class, [
				'class' => Author::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->orderBy('a.lastName', 'ASC');
				},
				'label' => 'book.author',
				'choice_label' => 'fullName',
				'multiple' => false
			])
			->add('genre', EntityType::class, [
				'class' => Genre::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('g')
						->orderBy('g.name', 'ASC');
				},
				'label' => 'book.genre',
				'choice_label' => 'name',
				'required' => false,
				'multiple' => false
			])
			->add('serie', EntityType::class, [
				'class' => Serie::class,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('s')
						->orderBy('s.name', 'ASC');
				},
				'label' => 'book.serie',
				'choice_label' => 'name',
				'required' => false,
				'multiple' => false
			])
			->add('annotation', TextareaType::class, [
				'label' => 'book.annotation',
				'required' => false
				])
			->add('file', FileType::class, [
				'label' => 'book.file',
				'required' => false
			]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Book::class,
		]);
	}


}