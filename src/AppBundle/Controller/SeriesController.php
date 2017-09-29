<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Serie;
use AppBundle\Form\SerieType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Filter\DTO\SerieFilter;
use AppBundle\Filter\Form\SerieFilterType;
use UnexpectedValueException;
use AppBundle\Service\Sessions;

class SeriesController extends Controller
{
    const LIMIT = 15;

	/**
	 * @Route("/series", name="series")
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('view', new Serie());

		$paginator = $this->get('knp_paginator');

		$seriesService = $this->get('app.series');

		$sessionService = $this->get('app.sessions');

		$filter = new SerieFilter();

		$form = $this->createForm(SerieFilterType::class, $filter, [
			'data_class' => SerieFilter::class
		]);
		$form->handleRequest($request);

		try {
			$sessionService->updateFilterFromSession($form, $filter);
		} catch (UnexpectedValueException $e) {
			$translator = $this->get('translator');
			$this->addFlash('error', $translator->trans($e->getMessage()));
		}

		if (null !== $request->get('reset')) {
			return $this->redirectToRoute("series");
		}

		$query = $seriesService->getFilteredSeries($filter);

		$series = $paginator->paginate(
			$query, $request->query->getInt('page', 1), self::LIMIT
		);

		return $this->render('series/index.html.twig', [
			'series' => $series,
			'form' => $form->createView(),
			'filterName' => $sessionService->getFilterName($filter)
		]);
	}

	/**
	 * @Route("/series/add", name="series_add")
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$serie = new Serie();

		$this->denyAccessUnlessGranted('create', $serie);

		return $this->processForm($request, $serie, 'messages.serie_added');
	}

	/**
	 * @Route("/series/edit/{id}", name="series_edit")
	 * @ParamConverter("serie")
	 *
	 * @param Request $request
	 * @param Serie $serie
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, Serie $serie)
	{
		$this->denyAccessUnlessGranted('edit', $serie);

		return $this->processForm($request, $serie, 'messages.changes_accepted');
	}

	/**
	 * @Route("/series/delete/{id}", name="series_delete")
	 * @ParamConverter("serie")
	 *
	 * @param Serie $serie
	 * @return RedirectResponse
	 */

	public function deleteAction(Serie $serie)
	{
		$this->denyAccessUnlessGranted('delete', $serie);

		$seriesService = $this->get('app.series');

		$translator = $this->get('translator');

		$seriesService->remove($serie);

		$this->addFlash('notice', $translator->trans('messages.serie_deleted'));

		return $this->redirectToRoute('series');
	}

	/**
	 * @param Request $request
	 * @param Serie $serie
	 * @param string $message
	 * @return RedirectResponse|Response
	 */
	private function processForm(Request $request, Serie $serie, $message)
	{
		$serieService = $this->get('app.series');

		$isNew = (null === $serie->getId()) ? true : false;

		$form = $this->createForm(SerieType::class, $serie);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$translator = $this->get('translator');

			$route = $isNew ? 'series' : 'series_edit';

			$serieService->save($serie);

			$this->addFlash('notice', $translator->trans($message));

			return $this->redirectToRoute($route, [
				'id' => $serie->getId()
			]);
		}

		return $this->render('series/form.html.twig', [
			'form' => $form->createView(),
			'serie' => $serie,
			'filterName' => $isNew ? null : Sessions::getFilterName(SerieFilter::class)
		]);
	}
}
