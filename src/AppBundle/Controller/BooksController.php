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
use AppBundle\Form\BookEditType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

		$bookService = $this->get('app.books');

		$filter = new BookFilter();

		$form = $this->createForm(BookFilterType::class, $filter);
		$form->handleRequest($request);

		if($form->isSubmitted() && !$form->isValid()) {
			$this->addFlash('error', 'Ошибка в параметрах фильтра.');
		}

		$query = $bookService->getFilteredBooks($filter);

		$books = $paginator->paginate(
			$query, $request->query->getInt('page', 1), 15
		);

		return $this->render('books/index.html.twig', [
			'form' => $form->createView(),
			'books' => $books
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
		$bookService = $this->get('app.books');

		$book = new Book();

		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$bookService->save($this->getUser(), $book);

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
		$bookService = $this->get('app.books');

		$form = $this->createForm(BookEditType::class, $book);
		$form->handleRequest($request);

		if($form->isSubmitted()) {

			$validator = $this->get('validator');
			$errors = $validator->validate($book, null, 'edit');

			if(!count($errors)) {
				$bookService->save($this->getUser(), $book, false);
			}

			$this->addFlash('notice', 'Изменения сохранены.');

			return $this->redirectToRoute('books', [
			]);
		}

		return $this->render('books/edit.html.twig', [
			'form' => $form->createView(),
			'book' => $book
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
		$bookService = $this->get('app.books');

		$bookService->remove($book);

		$this->addFlash('notice', 'Книга удалена');

		return $this->redirectToRoute('books');
	}

	/**
	 * @Route("/books/download/{id}", name="books_download")
	 * @ParamConverter("book")
	 *
	 * @param Book $book
	 * @return bool|BinaryFileResponse|NotFoundHttpException
	 */
	public function downloadAction(Book $book)
	{
		$bookService = $this->get('app.books');

		$response = $bookService->download($book);

		if(false === $response instanceof BinaryFileResponse) {
			return $this->createNotFoundException("Файл не найден.");
		}

		return $response;
	}

	/**
	 * @Route("/books/view/{id}", name="books_view")
	 * @ParamConverter("book")
	 *
	 * @param Book $book
	 * @return Response
	 */
	public function viewAction(Book $book)
	{
		return $this->render('books/view.html.twig', [
			'book' => $book
		]);
	}
}
