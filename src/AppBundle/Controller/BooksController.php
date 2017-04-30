<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Rating;
use AppBundle\Form\BookType;
use AppBundle\Form\RatingType;
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
use Symfony\Component\HttpFoundation\File\File;

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

		$sessionService = $this->get('app.sessions');

		$archiveService = $this->get('app.archives');

		$translator = $this->get('translator');

		$filter = new BookFilter();

		$form = $this->createForm(BookFilterType::class, $filter);
		$form->handleRequest($request);

		try {
			$sessionService->updateFilterFromSession($form, $filter);
		} catch (\UnexpectedValueException $e) {
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
		$bookService = $this->get('app.books');

		$book = new Book();

		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$bookService->save($this->getUser(), $book);

			$translator = $this->get('translator');

			$this->addFlash('notice', $translator->trans('messages.book_added'));

			return $this->redirectToRoute('books_add');
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
		$bookService = $this->get('app.books');

		$sessionService = $this->get('app.sessions');

		$originalFile = $book->getFile();
		$book->setFile(new File($originalFile));

		$form = $this->createForm(BookType::class, $book);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {

			$translator = $this->get('translator');

			$validator = $this->get('validator');
			$errors = $validator->validate($book, null, 'edit');

			if (0 === count($errors)) {
				if (empty($book->getFile())) {
					$book->setFile($originalFile);
					$bookService->save($this->getUser(), $book, false);
				} elseif ($form->isValid()) {
					$bookService->save($this->getUser(), $book, false, $originalFile);
				}
			}

			$this->addFlash('notice', $translator->trans('messages.changes_accepted'));

			return $this->redirectToRoute('books', [
			]);
		}

		return $this->render('books/form.html.twig', [
			'form' => $form->createView(),
			'book' => $book,
			'id' => $book->getId(),
			'filterName' => $sessionService->getFilterName(new BookFilter())
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

		$translator = $this->get('translator');

		$bookService->remove($book);

		$this->addFlash('notice', $translator->trans('messages.book_deleted'));

		return $this->redirectToRoute('books');
	}

	/**
	 * @Route("/books/download/{id}", name="books_download")
	 * @ParamConverter("book")
	 *
	 * @param Book $book
	 * @return BinaryFileResponse
	 */
	public function downloadAction(Book $book)
	{
		$bookService = $this->get('app.books');

		try {
			$response = $bookService->download($book);
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
	 * @param Request $request
	 * @param Book $book
	 * @return RedirectResponse|Response
	 */
	public function viewAction(Request $request, Book $book)
	{
		$comment = new Comment();
		$rating = new Rating();

		$validator = $this->get('validator');
		$metaData = $validator->getMetadataFor($comment)
			->properties['message'];

		$lengthConstraint = $metaData->constraints[0];

		$ratingForm = $this->createForm(RatingType::class, $rating);
		$ratingForm->handleRequest($request);

		if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
			$ratingService = $this->get('app.ratings');

			$translator = $this->get('translator');

			$ratingService->save($this->getUser(), $book, $rating);

			$this->addFlash('notice', $translator->trans('messages.vote_success'));
		}

		$commentForm = $this->createForm(CommentType::class, $comment);
		$commentForm->handleRequest($request);

		if ($commentForm->isSubmitted() && $commentForm->isValid()) {
			$commentService = $this->get('app.comments');

			$translator = $this->get('translator');

			$commentService->save($this->getUser(), $book, $comment);

			$this->addFlash('notice.comment', $translator->trans('messages.comment_added'));

			return $this->redirectToRoute('books_view', [
				'id' => $book->getId()
			]);
		}

		$paginator = $this->get('knp_paginator');

		$commentService = $this->get('app.comments');

		$query = $commentService->getQuery($book);

		$comments = $paginator->paginate(
			$query, $request->query->getInt('page', 1), $this->getParameter('comments_per_page')
		);

		return $this->render('books/view.html.twig', [
			'book' => $book,
			'form' => $ratingForm->createView(),
			'comment_form' => $commentForm->createView(),
			'comments' => $comments,
			'commentLength' => [
				'min' => $lengthConstraint->min,
				'max' => $lengthConstraint->max
			]
		]);
	}
}
