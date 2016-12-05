<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class BookEditType extends BookType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'Название'
			])
			->add('author', EntityType::class, [
				'class' => 'AppBundle:Author',
				'label' => 'Автор',
				'choice_label' => 'fullName',
				'multiple' => false,
			])
			->add('genre', EntityType::class, [
				'class' => 'AppBundle:Genre',
				'label' => 'Жанр',
				'choice_label' => 'name',
				'multiple' => false,
			])
			->add('file', HiddenType::class, []);
	}
}