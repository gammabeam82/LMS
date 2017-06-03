<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Comment;
use AppBundle\Entity\File;
use AppBundle\Form\BookEditType;
use AppBundle\Form\BookType;
use AppBundle\Form\CommentType;
use AppBundle\Filter\BookFilter;
use AppBundle\Filter\Form\BookFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnexpectedValueException;
use AppBundle\Service\Sessions;

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
		$this->denyAccessUnlessGranted('view', new Book());

		$paginator = $this->get('knp_paginator');

		$bookService = $this->get('app.books');

		$sessionService = $this->get('app.sessions');

		$archiveService = $this->get('app.archives');

		$filter = new BookFilter();

		$form = $this->createForm(BookFilterType::class, $filter);
		$form->handleRequest($request);

		try {
			$sessionService->updateFilterFromSession($form, $filter);
		} catch (UnexpectedValueException $e) {
			$translator = $this->get('translator');
			$this->addFlash('error', $translator->trans($e->getMessage()));
		}

		$query = $bookService->getFilteredBooks($filter);

		$books = $paginator->paginate(
			$query, $request->query->getInt('page', 1), $this->getParameter('books_per_page')
		);

		return $this->render('books/index.html.twig', [
			'form' => $form->createView(),
			'books' => $books,
			'booksInArchive' => $archiveService->getBookIds(),
			'filterName' => $sessionService->getFilterName($filter)
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
		$book = new Book();

		$this->denyAccessUnlessGranted('create', $book);

		$bookService = $this->get('app.books');

		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$translator = $this->get('translator');

			try {
				$bookService->save($this->getUser(), $book, true);
				$this->addFlash('notice', $translator->trans('messages.book_added'));
				return $this->redirectToRoute('books', [

				]);
			} catch (UnexpectedValueException $e) {
				$this->addFlash('error', $translator->trans('messages.upload_error'));
			}
		}

		return $this->render('books/form.html.twig', [
			'form' => $form->createView(),
			'book' => $book,
			'filterName' => null
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
		$this->denyAccessUnlessGranted('edit', $book);

		$bookService = $this->get('app.books');

		$form = $this->createForm(BookEditType::class, $book);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$translator = $this->get('translator');

			try {
				$bookService->save($this->getUser(), $book, false);
				$this->addFlash('notice', $translator->trans('messages.changes_accepted'));
				return $this->redirectToRoute('books_edit', [
					'id' => $book->getId()
				]);
			} catch (UnexpectedValueException $e) {
				$this->addFlash('error', $translator->trans('messages.upload_error'));
			}
		}

		return $this->render('books/form.html.twig', [
			'form' => $form->createView(),
			'book' => $book,
			'id' => $book->getId(),
			'filterName' => Sessions::getFilterName(BookFilter::class)
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
		$this->denyAccessUnlessGranted('delete', $book);

		$bookService = $this->get('app.books');

		$translator = $this->get('translator');

		$bookService->remove($book);

		$this->addFlash('notice', $translator->trans('messages.book_deleted'));

		return $this->redirectToRoute('books');
	}

	/**
	 * @Route("/books/file/delete/{id}", name="books_file_delete")
	 * @ParamConverter("file")
	 *
	 * @param File $file
	 * @return RedirectResponse
	 */
	public function deleteBookFileAction(File $file)
	{
		$this->denyAccessUnlessGranted('delete', $file->getBook());

		$bookService = $this->get('app.books');

		$translator = $this->get('translator');

		$bookService->removeFile($file);

		$this->addFlash('notice', $translator->trans('messages.changes_accepted'));

		return $this->redirectToRoute('books_edit', [
			'id' => $file->getBook()->getId()
		]);
	}

	/**
	 * @Route("/books/file/download/{id}", name="books_file_download")
	 * @ParamConverter("file")
	 *
	 * @param File $file
	 * @return BinaryFileResponse
	 */
	public function downloadBookFileAction(File $file)
	{
		$this->denyAccessUnlessGranted('view', $file->getBook());

		$bookService = $this->get('app.books');

		try {
			$response = $bookService->downloadFile($file);
		} catch (\LogicException $e) {
			$translator = $this->get('translator');
			throw $this->createNotFoundException($translator->trans('messages.file_not_found'));
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
		$this->denyAccessUnlessGranted('view', $book);

		$bookService = $this->get('app.books');

		return $this->render('books/view.html.twig', [
			'book' => $book,
			'images' => $bookService->getImages($book)
		]);
	}
}
