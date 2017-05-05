<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Registry;
use UnexpectedValueException;
use BadMethodCallException;

class Sessions
{
	/**
	 * @var RequestStack
	 */
	private $requestStack;

	/**
	 * @var Registry
	 */
	private $doctrine;

	/**
	 * Sessions constructor.
	 * @param RequestStack $requestStack
	 * @param Registry $doctrine
	 */
	public function __construct(RequestStack $requestStack, Registry $doctrine)
	{
		$this->requestStack = $requestStack;
		$this->doctrine = $doctrine;
	}

	/**
	 * @param $form
	 * @param $filter
	 */
	public function updateFilterFromSession($form, $filter)
	{
		$request = $this->requestStack->getCurrentRequest();

		$session = $request->getSession();

		$filterName = $this->getFilterName($filter);

		if (null !== $request->get('reset')) {
			$session->remove($filterName);
		}

		/**
		 * @var \Symfony\Component\Form\Form $form
		 */
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
	 * @param $filter
	 * @return string
	 */
	public function getFilterName($filter)
	{
		$filterName = (is_object($filter)) ? get_class($filter) : $filter;
		return substr(md5($filterName), 0, 10);
	}
}