<?php

namespace AppBundle\Service;

use AppBundle\Filter\FilterInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RequestStack;
use UnexpectedValueException;
use BadMethodCallException;

class Sessions extends AbstractService
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
     * @param Form $form
     * @param FilterInterface $filter
     */
    public function updateFilterFromSession(Form $form, FilterInterface $filter): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $session = $request->getSession();

        $filterName = self::getFilterName($filter);

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
                $data = unserialize($session->get($filterName));
                $em = $this->doctrine->getManager();

                foreach ($form as $field) {

                    $fieldName = ucfirst($field->getName());
                    $getter = 'get' . $fieldName;
                    $setter = 'set' . $fieldName;

                    if (false === method_exists($data, $getter)) {
                        throw new BadMethodCallException();
                    }

                    $value = $data->$getter();

                    if (is_array($value) && isset($value[0]) && is_object($value[0])) {
                        $entities = [];
                        foreach ($value as $entity) {
                            $entities[] = $em->merge($entity);
                        }
                        $field->setData($entities);
                    } else {
                        $field->setData($value);
                    }

                    if (false === method_exists($filter, $setter)) {
                        throw new BadMethodCallException();
                    }

                    $filter->$setter($value);
                }
            }
        }
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
