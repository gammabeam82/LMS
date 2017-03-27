<?php

namespace AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BookType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'book.name'
			])
			->add('author', EntityType::class, [
				'class' => 'AppBundle:Author',
				'label' => 'book.author',
				'choice_label' => 'fullName',
				'multiple' => false,
			])
			->add('genre', EntityType::class, [
				'class' => 'AppBundle:Genre',
				'label' => 'book.genre',
				'choice_label' => 'name',
				'required' => false,
				'multiple' => false
			])
			->add('file', FileType::class, [
				'label' => 'book.file',
				'required' => false
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Book',
		]);
	}


}