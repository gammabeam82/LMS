<?php

namespace AppBundle\Form;

use AppBundle\Entity\Book;
use AppBundle\Entity\Author;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookType extends AbstractType
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
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Book',
		]);
	}


}