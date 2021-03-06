<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genre;
use AppBundle\Filter\DTO\GenreFilter;
use AppBundle\Filter\Form\GenreFilterType;
use AppBundle\Form\GenreType;
use AppBundle\Security\Actions;
use AppBundle\Service\Sessions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class GenresController extends Controller
{
    private const LIMIT = 15;

    /**
     * @Route("/genres", name="genres")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Genre());

        $paginator = $this->get('knp_paginator');

        $genresService = $this->get('app.genres');

        $sessionService = $this->get('app.sessions');

        $filter = new GenreFilter();

        $form = $this->createForm(GenreFilterType::class, $filter, [
            'data_class' => GenreFilter::class
        ]);
        $form->handleRequest($request);

        try {
            $sessionService->updateFilterFromSession($form, $filter);
        } catch (UnexpectedValueException $e) {
            $translator = $this->get('translator');
            $this->addFlash('error', $translator->trans($e->getMessage()));
        }

        if (null !== $request->get('reset') || null !== $request->get('id')) {
            return $this->redirectToRoute("genres");
        }

        $query = $genresService->getFilteredGenres($filter);

        $genres = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return $this->render('genres/list.html.twig', [
            'genres' => $genres,
            'form' => $form->createView(),
            'filterName' => $sessionService->getFilterName($filter)
        ]);
    }

    /**
     * @Route("/genres/add", name="genres_add")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $genre = new Genre();

        $this->denyAccessUnlessGranted(Actions::CREATE, $genre);

        return $this->processForm($request, $genre, 'messages.genre_added');
    }

    /**
     * @Route("/genres/edit/{id}", name="genres_edit")
     * @ParamConverter("genre")
     *
     * @param Request $request
     * @param Genre $genre
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Genre $genre)
    {
        $this->denyAccessUnlessGranted(Actions::EDIT, $genre);

        return $this->processForm($request, $genre, 'messages.changes_accepted');
    }

    /**
     * @Route("/genres/delete/{id}", name="genres_delete")
     * @ParamConverter("genre")
     *
     * @param Genre $genre
     *
     * @return RedirectResponse
     */
    public function deleteAction(Genre $genre)
    {
        $this->denyAccessUnlessGranted(Actions::DELETE, $genre);

        $genreService = $this->get('app.genres');

        $translator = $this->get('translator');

        $genreService->remove($genre);

        $this->addFlash('notice', $translator->trans('messages.genre_deleted'));

        return $this->redirectToRoute('genres');
    }

    /**
     * @param Request $request
     * @param Genre $genre
     * @param string $message
     * @return RedirectResponse|Response
     */
    private function processForm(Request $request, Genre $genre, $message)
    {
        $genreService = $this->get('app.genres');

        $isNew = (null === $genre->getId());

        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $translator = $this->get('translator');

            $route = $isNew ? 'genres' : 'genres_edit';

            $genreService->save($genre);

            $this->addFlash('notice', $translator->trans($message));

            return $this->redirectToRoute($route, [
                'id' => $genre->getId()
            ]);
        }

        return $this->render('genres/form.html.twig', [
            'form' => $form->createView(),
            'genre' => $genre,
            'filterName' => $isNew ? null : Sessions::getFilterName(GenreFilter::class)
        ]);
    }
}
