<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Serie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('bookFiles', CollectionType::class, [
                'entry_type' => BookFileType::class,
                'required' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => ' ',
                'mapped' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'csrf_protection' => false
        ]);
    }
}
