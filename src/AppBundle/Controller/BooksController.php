<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BooksController extends Controller
{
	/**
	 * @Route("/books", name="books")
	 */
	public function indexAction()
	{
		return $this->render('books/index.html.twig', [ ]);
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
			$books->add($this->getUser(), $book);

			$this->addFlash('notice', 'Книга добавлена.');

			return $this->redirectToRoute('books_add');
		}

		return $this->render('books/form.html.twig', [
			'form' => $form->createView()
		]);
	}
}
