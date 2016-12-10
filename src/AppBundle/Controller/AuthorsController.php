<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Filter\AuthorFilter;
use AppBundle\Filter\Form\AuthorFilterType;

class AuthorsController extends Controller
{
	/**
	 * @Route("/authors", name="authors")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function indexAction(Request $request)
	{
		$paginator = $this->get('knp_paginator');

		$authorService = $this->get('app.authors');

		$filter = new AuthorFilter();

		$form = $this->createForm(AuthorFilterType::class, $filter);
		$form->handleRequest($request);

		if($form->isSubmitted() && !$form->isValid()) {
			$this->addFlash('error', 'Ошибка в параметрах фильтра.');
		}

		$query = $authorService->getFilteredAuthors($filter);

		$authors = $paginator->paginate(
			$query, $request->query->getInt('page', 1), 15
		);

		return $this->render('authors/index.html.twig', [
			'authors' => $authors,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/authors/add", name="authors_add")
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$authorService = $this->get('app.authors');

		$author = new Author();

		$form = $this->createForm(AuthorType::class, $author);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$authorService->save($this->getUser(), $form->getData());

			$this->addFlash('notice', 'Автор добавлен.');

			return $this->redirectToRoute('authors_add');
		}

		return $this->render('authors/form.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/authors/edit/{id}", name="authors_edit")
	 * @ParamConverter("author")
	 */
	public function editAction(Request $request, Author $author)
	{
		$authorService = $this->get('app.authors');

		$form = $this->createForm(AuthorType::class, $author);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$authorService->save($this->getUser(), $form->getData(), false);

			$this->addFlash('notice', 'Изменения сохранены.');

			return $this->redirectToRoute('authors');
		}

		return $this->render('authors/edit.html.twig', [
			'form' => $form->createView(),
			'author' => $author
		]);
	}

	/**
	 * @Route("/authors/delete/{id}", name="authors_delete")
	 * @ParamConverter("author")
	 *
	 * @param Author $author
	 * @return RedirectResponse
	 */
	public function deleteAction(Author $author)
	{
		$authorService = $this->get('app.authors');

		$authorService->remove($author);

		$this->addFlash('notice', 'Автор удален.');

		return $this->redirectToRoute('authors');
	}
}
