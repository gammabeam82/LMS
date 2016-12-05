<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Filter\BookFilter;
use AppBundle\Filter\Form\BookFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BooksController extends Controller
{
	/**
	 * @Route("/books", name="books")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function indexAction(Request $request)
	{
		$paginator = $this->get('knp_paginator');

		$books = $this->get('app.books');

		$filter = new BookFilter();

		$form = $this->createForm(BookFilterType::class, $filter);
		$form->handleRequest($request);

		$query = $books->getFilteredBooks($filter);

		$pagination = $paginator->paginate(
			$query, $request->query->getInt('page', 1), 10
		);

		return $this->render('books/index.html.twig', [
			'form' => $form->createView(),
			'pagination' => $pagination
		]);
	}

	/**
	 * @Route("/books/add", name="books_add")
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$books = $this->get('app.books');

		$book = new Book();

		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$books->save($this->getUser(), $book);

			$this->addFlash('notice', 'Книга добавлена.');

			return $this->redirectToRoute('books_add');
		}

		return $this->render('books/form.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/books/edit/{id}", name="books_edit")
	 * @ParamConverter("book")
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, Book $book)
	{
		$books = $this->get('app.books');

		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			//TODO сделать сохранение

			$this->addFlash('notice', 'Изменения сохранены.');

			return $this->redirectToRoute('books_edit', [
				'id' => $book->getId()
			]);
		}

		return $this->render('books/edit.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/books/delete/{id}", name="books_delete")
	 * @ParamConverter("book")
	 *
	 * @param Book $book
	 * @return RedirectResponse
	 */
	public function deleteAction(Book $book)
	{
		$books = $this->get('app.books');

		$books->remove($book);

		$this->addFlash('notice', 'Книга удалена');
		return $this->redirectToRoute('books');
	}

	/**
	 * @Route("/books/download/{id}", name="books_download")
	 * @ParamConverter("book")
	 */
	public function downloadAction(Book $book)
	{
		$fileName = $this->getParameter("library")."/".$book->getFile();

		$response = new BinaryFileResponse($fileName);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

		return $response;
	}
}
