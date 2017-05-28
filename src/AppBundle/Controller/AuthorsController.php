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
use UnexpectedValueException;
use AppBundle\Utils\MbRangeTrait;
use AppBundle\Service\Sessions;

class AuthorsController extends Controller
{
	use MbRangeTrait;

	/**
	 * @Route("/authors", name="authors")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('view', new Author());

		$paginator = $this->get('knp_paginator');

		$authorService = $this->get('app.authors');

		$sessionService = $this->get('app.sessions');

		$filter = new AuthorFilter();

		$form = $this->createForm(AuthorFilterType::class, $filter);
		$form->handleRequest($request);

		try {
			$sessionService->updateFilterFromSession($form, $filter);
		} catch (UnexpectedValueException $e) {
			$translator = $this->get('translator');
			$this->addFlash('error', $translator->trans($e->getMessage()));
		}

		$query = $authorService->getFilteredAuthors($filter);

		$authors = $paginator->paginate(
			$query, $request->query->getInt('page', 1), $this->getParameter('authors_per_page')
		);

		return $this->render('authors/index.html.twig', [
			'authors' => $authors,
			'form' => $form->createView(),
			'filterName' => $sessionService->getFilterName($filter),
			'letters' => $this->mb_range('а', 'я')
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
		$author = new Author();

		$this->denyAccessUnlessGranted('create', $author);

		$authorService = $this->get('app.authors');

		$form = $this->createForm(AuthorType::class, $author);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$authorService->save($this->getUser(), $author);

			$translator = $this->get('translator');

			$this->addFlash('notice', $translator->trans('messages.author_added'));

			return $this->redirectToRoute('authors_add');
		}

		return $this->render('authors/form.html.twig', [
			'form' => $form->createView(),
			'author' => $author,
			'filterName' => null
		]);
	}

	/**
	 * @Route("/authors/edit/{id}", name="authors_edit")
	 * @ParamConverter("author")
	 *
	 * @param Request $request
	 * @param Author $author
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, Author $author)
	{
		$this->denyAccessUnlessGranted('edit', $author);

		$authorService = $this->get('app.authors');

		$form = $this->createForm(AuthorType::class, $author);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$translator = $this->get('translator');

			$authorService->save($this->getUser(), $author, false);

			$this->addFlash('notice', $translator->trans('messages.changes_accepted'));

			return $this->redirectToRoute('authors');
		}

		return $this->render('authors/form.html.twig', [
			'form' => $form->createView(),
			'author' => $author,
			'filterName' => Sessions::getFilterName(AuthorFilter::class)
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
		$this->denyAccessUnlessGranted('remove', $author);

		$authorService = $this->get('app.authors');

		$translator = $this->get('translator');

		$authorService->remove($author);

		$this->addFlash('notice', $translator->trans('messages.author_deleted'));

		return $this->redirectToRoute('authors');
	}
}
