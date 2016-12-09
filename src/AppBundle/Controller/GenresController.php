<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Genre;
use AppBundle\Form\GenreType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GenresController extends Controller
{
	/**
	 * @Route("/genres", name="genres")
	 */
	public function indexAction()
	{
		return $this->render('genres/index.html.twig', [ ]);
	}

	/**
	 * @Route("/genres/add", name="genres_add")
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{
		$genreService = $this->get('app.genres');

		$genre = new Genre();

		$form = $this->createForm(GenreType::class, $genre);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$genreService->add($this->getUser(), $form->getData());

			$this->addFlash('notice', 'Жанр добавлен.');

			return $this->redirectToRoute('genres_add');
		}

		return $this->render('genres/form.html.twig', [
			'form' => $form->createView()
		]);
	}
}
