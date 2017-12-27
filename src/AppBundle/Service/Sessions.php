<?php

namespace AppBundle\Service;

use AppBundle\Filter\FilterInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use UnexpectedValueException;

class Sessions extends BaseService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Sessions constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param FormInterface $form
     * @param FilterInterface $filter
     */
    public function updateFilterFromSession(FormInterface $form, FilterInterface $filter): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $session = $request->getSession();

        $filterName = $this->getFilterName($filter);

        if (null !== $request->get('reset')) {
            $session->remove($filterName);
        }

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $session->set($filterName, serialize($form->getData()));
            } else {
                throw new UnexpectedValueException('messages.filter_error');
            }
        } else {
            if (null !== $session->get($filterName)) {

                $restoredFilter = unserialize($session->get($filterName));
                $em = $this->doctrine->getManager();
                $accessor = PropertyAccess::createPropertyAccessor();

                foreach ($form as $field) {
                    $property = $field->getName();
                    $value = $accessor->getValue($restoredFilter, $property);
                    if (false !== $this->isEntityType($value)) {
                        $entities = [];
                        foreach ($value as $entity) {
                            $entities[] = $em->merge($entity);
                        }
                        $field->setData($entities);
                    } else {
                        $field->setData($value);
                    }
                    try {
                        $accessor->setValue($filter, $property, $value);
                    } catch (\TypeError $e) {
                        throw new UnexpectedValueException('messages.filter_error');
                    }
                }
            }
        }
    }

    /**
     * @param mixed $item
     * @return bool
     */
    private function isEntityType($item): bool
    {
        return is_array($item) && isset($item[0]) && is_object($item[0]);
    }

    /**
     * @param FilterInterface|string $filter
     * @return string
     */
    public static function getFilterName($filter): string
    {
        $filterName = (is_object($filter)) ? get_class($filter) : $filter;

        return substr(md5($filterName), 0, 10);
    }
}
