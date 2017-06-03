<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Book;
use AppBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentsController extends Controller
{
	/**
	 * @Route("/books/{id}/comments", name="books_comments")
	 * @ParamConverter("book")
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return RedirectResponse|Response
	 */
	public function indexAction(Request $request, Book $book)
	{
		$masterRequest = $this->get('request_stack')->getMasterRequest();

		$comment = new Comment();

		$commentService = $this->get('app.comments');

		$paginator = $this->get('knp_paginator');

		$validator = $this->get('validator');
		$metaData = $validator->getMetadataFor($comment)
			->properties['message'];

		$lengthConstraint = $metaData->constraints[0];

		$commentForm = $this->createForm(CommentType::class, $comment, [
			'action' => $this->generateUrl('books_comments', [
				'id' => $book->getId()
			])
		]);

		$commentForm->handleRequest($request);

		if ($commentForm->isSubmitted() && $commentForm->isValid()) {

			$translator = $this->get('translator');

			$commentService->save($this->getUser(), $book, $comment);

			$this->addFlash('notice.comment', $translator->trans('messages.comment_added'));

			return $this->redirectToRoute('books_view', [
				'id' => $book->getId()
			]);
		}

		$query = $commentService->getQuery($book);

		$comments = $paginator->paginate(
			$query, $masterRequest->query->getInt('page', 1), $this->getParameter('comments_per_page')
		);

		return $this->render('comments/index.html.twig', [
			'comment_form' => $commentForm->createView(),
			'comments' => $comments,
			'commentLength' => [
				'min' => $lengthConstraint->min,
				'max' => $lengthConstraint->max
			]
		]);

	}

	/**
	 * @Route("/books/{id}/comments/delete/{comment_id}", name="comments_delete")
	 * @ParamConverter("book", class="AppBundle:Book", options={"id" = "id"})
	 * @ParamConverter("comment", class="AppBundle:Comment", options={"id" = "comment_id"})
	 *
	 * @param Book $book
	 * @param Comment $comment
	 * @return RedirectResponse
	 */
	public function deleteAction(Book $book, Comment $comment)
	{
		$this->denyAccessUnlessGranted('delete', $comment);

		$commentService = $this->get('app.comments');

		$commentService->remove($comment);

		$translator = $this->get('translator');

		$this->addFlash('notice.comment', $translator->trans('messages.comment_deleted'));

		return $this->redirectToRoute('books_view', [
			'id' => $book->getId()
		]);
	}

	/**
	 * @Route("/books/{id}/comments/edit/{comment_id}", name="comments_edit")
	 * @ParamConverter("book", class="AppBundle:Book", options={"id" = "id"})
	 * @ParamConverter("comment", class="AppBundle:Comment", options={"id" = "comment_id"})
	 *
	 * @param Book $book
	 * @param Comment $comment
	 * @return RedirectResponse
	 */
	public function editAction(Book $book, Comment $comment)
	{
		$this->denyAccessUnlessGranted('edit', $comment);


	}
}