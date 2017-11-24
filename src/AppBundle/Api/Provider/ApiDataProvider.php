<?php

namespace AppBundle\Api\Provider;

use AppBundle\Api\Transformer\TransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

class ApiDataProvider
{
    /**
     * @var array
     */
    private $options;

    /**
     * DataProvider constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $collection = new Collection($this->options['items'], $this->options['transformer']);

        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());

        $data = $manager->createData($collection)->toArray();

        $result = [
            'items' => $data['data'],
            'totalCount' => count($data['data'])
        ];

        return $result;
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('transformer');
        $resolver->setAllowedTypes('transformer', ['object']);
        $resolver->setAllowedValues('transformer', function ($value) {
            return $value instanceof TransformerInterface;
        });

        $resolver->setRequired('items');
        $resolver->setAllowedTypes('items', ['array', 'object']);
        $resolver->setAllowedValues('items', function ($value) {
            return is_array($value) || $value instanceof \Iterator;
        });
    }
}
