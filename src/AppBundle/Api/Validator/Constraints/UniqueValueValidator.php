<?php

namespace AppBundle\Api\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueValueValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UniqueValueValidator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $value
     *
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (false === is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $repo = $this->em->getRepository($constraint->entityClass);

        $searchResults = $repo->findBy([
            $constraint->field => $value
        ]);

        if (count($searchResults) > 0) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
