<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Genre;
use AppBundle\Form\GenreType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class GenresController extends Controller
{
	/**
	 * @Route("/genres", name="genres")
	 */
	public function indexAction(Request $request)
	{
		$paginator = $this->get('knp_paginator');

		$genreService = $this->get('app.genres');

		$query = $genreService->getQuery();

		$genres = $paginator->paginate(
			$query, $request->query->getInt('page', 1), 15
		);

		return $this->render('genres/index.html.twig', [
			'genres' => $genres
		]);
	}

	/**
	 * @Route("/genres/add", name="genres_add")
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$genreService = $this->get('app.genres');

		$genre = new Genre();

		$form = $this->createForm(GenreType::class, $genre);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$genreService->save($this->getUser(), $form->getData());

			$this->addFlash('notice', 'Жанр добавлен.');

			return $this->redirectToRoute('genres_add');
		}

		return $this->render('genres/form.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/genres/edit/{id}", name="genres_edit")
	 * @ParamConverter("genre")
	 *
	 * @param Request $request
	 * @param Genre $genre
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, Genre $genre)
	{
		$genreService = $this->get('app.genres');

		$form = $this->createForm(GenreType::class, $genre);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$genreService->save($this->getUser(), $form->getData(), false);

			$this->addFlash('notice', 'Изменения сохранены.');

			return $this->redirectToRoute('genres');
		}

		return $this->render('genres/edit.html.twig', [
			'form' => $form->createView(),
			'genre' => $genre
		]);
	}

	/**
	 * @Route("/genres/delete/{id}", name="genres_delete")
	 * @ParamConverter("genre")
	 *
	 * @param Genre $genre
	 * @return RedirectResponse
	 */
	public function deleteAction(Genre $genre)
	{
		$genreService = $this->get('app.genres');

		$genreService->remove($genre);

		$this->addFlash('notice', 'Жанр удален.');

		return $this->redirectToRoute('genres');
	}
}
