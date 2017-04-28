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
use AppBundle\Filter\SerieFilter;
use AppBundle\Filter\Form\SerieFilterType;

class SeriesController extends Controller
{

	/**
	 * @Route("/series", name="series")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function indexAction(Request $request)
	{
		$paginator = $this->get('knp_paginator');

		$seriesService = $this->get('app.series');

		$sessionService = $this->get('app.sessions');

		$translator = $this->get('translator');

		$filter = new SerieFilter();

		$form = $this->createForm(SerieFilterType::class, $filter, [
			'data_class' => 'AppBundle\Filter\SerieFilter'
		]);
		$form->handleRequest($request);

		if(false === $sessionService->updateFilterFromSession($form, $filter)) {
			$this->addFlash('error', $translator->trans('messages.filter_error'));
		}

		$query = $seriesService->getFilteredSeries($filter);

		$series = $paginator->paginate(
			$query, $request->query->getInt('page', 1), $this->getParameter('series_per_page')
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
		$seriesService = $this->get('app.series');

		$serie = new Serie();

		$form = $this->createForm(SerieType::class, $serie);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$translator = $this->get('translator');

			$seriesService->save($serie);

			$this->addFlash('notice', $translator->trans('messages.serie_added'));

			return $this->redirectToRoute('series_add');
		}

		return $this->render('series/form.html.twig', [
			'form' => $form->createView(),
			'serie' => $serie,
			'filterName' => null
		]);
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
		$seriesService = $this->get('app.series');

		$sessionService = $this->get('app.sessions');

		$form = $this->createForm(SerieType::class, $serie);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$translator = $this->get('translator');

			$seriesService->save($serie);

			$this->addFlash('notice', $translator->trans('messages.changes_accepted'));

			return $this->redirectToRoute('series');
		}

		return $this->render('series/form.html.twig', [
			'form' => $form->createView(),
			'serie' => $serie,
			'filterName' => $sessionService->getFilterName(new SerieFilter())
		]);
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
		$seriesService = $this->get('app.series');

		$translator = $this->get('translator');

		$seriesService->remove($serie);

		$this->addFlash('notice', $translator->trans('messages.serie_deleted'));

		return $this->redirectToRoute('series');
	}

}
