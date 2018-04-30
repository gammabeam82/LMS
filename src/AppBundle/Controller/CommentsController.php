<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use AppBundle\Security\Actions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsController extends Controller
{
    private const LIMIT = 5;

    /**
     * @Route("/books/{id}/comments", name="books_comments")
     * @ParamConverter("book")
     *
     * @param Request $request
     * @param Book $book
     *
     * @return RedirectResponse|Response
     */
    public function listAction(Request $request, Book $book)
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

            return $this->redirectToRoute('books_show', [
                'id' => $book->getId()
            ]);
        }

        $query = $commentService->getQuery($book);

        $comments = $paginator->paginate(
            $query,
            $masterRequest->query->getInt('page', 1),
            self::LIMIT
        );

        return $this->render('comments/list.html.twig', [
            'comment_form' => $commentForm->createView(),
            'comments' => $comments,
            'commentLength' => [
                'min' => $lengthConstraint->min,
                'max' => $lengthConstraint->max
            ],
            'page' => $masterRequest->query->getInt('page', 1)
        ]);
    }

    /**
     * @Route("/books/{id}/comments/delete/{comment_id}", name="comments_delete")
     * @ParamConverter("book", class="AppBundle:Book", options={"id" = "id"})
     * @ParamConverter("comment", class="AppBundle:Comment", options={"id" = "comment_id"})
     *
     * @param Book $book
     * @param Comment $comment
     *
     * @return RedirectResponse
     */
    public function deleteAction(Book $book, Comment $comment)
    {
        $this->denyAccessUnlessGranted(Actions::DELETE, $comment);

        $commentService = $this->get('app.comments');

        $commentService->remove($comment);

        $translator = $this->get('translator');

        $this->addFlash('notice.comment', $translator->trans('messages.comment_deleted'));

        return $this->redirectToRoute('books_show', [
            'id' => $book->getId()
        ]);
    }

    /**
     * @Route("/books/{id}/comments/edit/{comment_id}", name="comments_edit")
     * @ParamConverter("book", class="AppBundle:Book", options={"id" = "id"})
     * @ParamConverter("comment", class="AppBundle:Comment", options={"id" = "comment_id"})
     *
     * @param Request $request
     * @param Book $book
     * @param Comment $comment
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Book $book, Comment $comment)
    {
        $this->denyAccessUnlessGranted(Actions::EDIT, $comment);

        $commentForm = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comments_edit', [
                'id' => $book->getId(),
                'comment_id' => $comment->getId(),
                'page' => $request->query->getInt('page', 1)
            ])
        ]);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted()) {
            $translator = $this->get('translator');

            if ($commentForm->isValid()) {
                $commentService = $this->get('app.comments');

                $commentService->save($this->getUser(), $book, $comment);

                $this->addFlash('notice.comment', $translator->trans('messages.changes_accepted'));
            } else {
                $this->addFlash('error.comment', $translator->trans('messages.error'));
            }

            return $this->redirectToRoute('books_show', [
                'id' => $book->getId(),
                'page' => $request->query->getInt('page', 1)
            ]);
        }

        return $this->render('comments/form.html.twig', [
            'comment_edit_form' => $commentForm->createView()
        ]);
    }
}
