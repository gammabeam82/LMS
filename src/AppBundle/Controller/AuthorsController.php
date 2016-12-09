<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthorsController extends Controller
{
	/**
	 * @Route("/authors", name="authors")
	 */
	public function indexAction()
	{
		return $this->render('authors/index.html.twig', [ ]);
	}

	/**
	 * @Route("/authors/add", name="authors_add")
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$authorService = $this->get('app.authors');

		$author = new Author();

		$form = $this->createForm(AuthorType::class, $author);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$authorService->add($this->getUser(), $form->getData());

			$this->addFlash('notice', 'Автор добавлен.');

			return $this->redirectToRoute('authors_add');
		}

		return $this->render('authors/form.html.twig', [
			'form' => $form->createView()
		]);
	}
}
