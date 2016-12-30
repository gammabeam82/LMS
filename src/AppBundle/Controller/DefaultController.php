<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Filter\BookFilter;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction()
	{
		$paginator = $this->get('knp_paginator');

		$bookService = $this->get('app.books');

		$commentService = $this->get('app.comments');

		$books = $paginator->paginate($bookService->getFilteredBooks(new BookFilter()), 1, 5);

		$comments = $paginator->paginate($commentService->getQuery(), 1, 5);

		return $this->render('default/index.html.twig',
			[
				'books' => $books,
				'comments' => $comments
			]
		);
	}
}
