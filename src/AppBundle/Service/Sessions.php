<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Registry;

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
	 */
	public function __construct(RequestStack $requestStack, Registry $doctrine)
	{
		$this->requestStack = $requestStack;
		$this->doctrine = $doctrine;
	}

	/**
	 * @param $form
	 * @param $filter
	 * @return bool
	 */
	public function updateFilterFromSession($form, $filter)
	{
		$request = $this->requestStack->getCurrentRequest();

		$session = $request->getSession();

		$filterName = substr(md5(get_class($filter)), 0, 10);

		if ($request->get('reset')) {
			$session->remove($filterName);
		}

		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$session->set($filterName, serialize($form->getData()));
			} else {
				return false;
			}
		} else {
			if ($session->get($filterName)) {
				$data = unserialize($session->get($filterName));
				$em = $this->doctrine->getManager();

				foreach ($form as $field) {

					$fieldName = ucfirst($field->getName());
					$getter = 'get' . $fieldName;
					$setter = 'set' . $fieldName;

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

					$filter->$setter($value);
				}
				return true;
			}
		}
	}
}