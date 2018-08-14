<?php

namespace AppBundle\Api\ArgumentResolver;

use AppBundle\Api\Exception\InvalidRequestException;
use AppBundle\Api\Request\RequestObject;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestObjectResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * RequestObjectResolver constructor.
     *
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return false !== is_subclass_of($argument->getType(), RequestObject::class);
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return Generator
     * @throws InvalidRequestException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $data = $request->getContent();

        if (Request::METHOD_GET === $request->getMethod()) {
            $data = json_encode($request->query->all());
        }

        if (empty($data)) {
            $data = json_encode([]);
        }

        $dto = $this->serializer->deserialize($data, $argument->getType(), 'json');

        $errors = $this->validator->validate($dto);

        if (0 !== count($errors)) {
            throw new InvalidRequestException($errors);
        }

        yield $dto;
    }
}
