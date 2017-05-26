<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class BookEditType extends BookType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);

		$builder
			->get('bookFiles')
			->setMapped(false);
	}
}