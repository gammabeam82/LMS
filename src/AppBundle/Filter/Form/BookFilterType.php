<?php

namespace AppBundle\Filter\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookFilterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'Книга',
				'required' => false
			])
			->add('author', EntityType::class, [
				'class' => 'AppBundle:Author',
				'label' => 'Автор',
				'choice_label' => 'shortName',
				'multiple' => false,
				'required' => false
			])
			->add('genre', EntityType::class, [
				'class' => 'AppBundle:Genre',
				'label' => 'Жанр',
				'choice_label' => 'name',
				'multiple' => false,
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