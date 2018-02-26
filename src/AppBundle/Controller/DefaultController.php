<?php

namespace AppBundle\Controller;

use AppBundle\Filter\DTO\BookFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function indexAction()
    {
        $paginator = $this->get('knp_paginator');

        $bookService = $this->get('app.books');

        $commentService = $this->get('app.comments');

        $books = $paginator->paginate($bookService->getFilteredBooks(new BookFilter(), $this->getUser()), 1, 30);

        $comments = $paginator->paginate($commentService->getQuery(), 1, 5);

        return $this->render(
            'default/index.html.twig',
            [
                'books' => $books,
                'comments' => $comments
            ]
        );
    }
}
