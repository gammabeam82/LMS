<?php

namespace AppBundle\Filter\Form;

use AppBundle\Filter\DTO\SerieFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'messages.name',
                'required' => false
            ])
            ->add('sortByName', CheckboxType::class, [
                'label' => 'messages.sort_by_name',
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SerieFilter::class,
            'csrf_protection' => false,
            'method' => 'GET'
        ]);
    }
}
