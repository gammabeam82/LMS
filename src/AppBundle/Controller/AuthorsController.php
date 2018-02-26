<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Filter\DTO\AuthorFilter;
use AppBundle\Filter\Form\AuthorFilterType;
use AppBundle\Form\AuthorType;
use AppBundle\Security\Actions;
use AppBundle\Service\Sessions;
use AppBundle\Utils\MbRangeTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class AuthorsController extends Controller
{
    use MbRangeTrait;

    private const LIMIT = 15;

    /**
     * @Route("/authors", name="authors")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Author());

        $paginator = $this->get('knp_paginator');

        $authorService = $this->get('app.authors');

        $sessionService = $this->get('app.sessions');

        $filter = new AuthorFilter();

        $form = $this->createForm(AuthorFilterType::class, $filter);
        $form->handleRequest($request);

        try {
            $sessionService->updateFilterFromSession($form, $filter);
        } catch (UnexpectedValueException $e) {
            $translator = $this->get('translator');
            $this->addFlash('error', $translator->trans($e->getMessage()));
        }

        if (null !== $request->get('reset') || null !== $request->get('id')) {
            return $this->redirectToRoute("authors");
        }

        $query = $authorService->getFilteredAuthors($filter);

        $authors = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return $this->render('authors/index.html.twig', [
            'authors' => $authors,
            'form' => $form->createView(),
            'filterName' => $sessionService->getFilterName($filter),
            'letters' => $this->mb_range('а', 'я')
        ]);
    }

    /**
     * @Route("/authors/view/{id}", name="authors_view")
     * @ParamConverter("author")
     *
     * @param Author $author
     * @return Response
     */
    public function viewAction(Author $author): Response
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, $author);

        return $this->render('authors/view.html.twig', [
            'author' => $author
        ]);
    }

    /**
     * @Route("/authors/add", name="authors_add")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $author = new Author();

        $this->denyAccessUnlessGranted(Actions::CREATE, $author);

        return $this->processForm($request, $author, 'messages.author_added');
    }

    /**
     * @Route("/authors/edit/{id}", name="authors_edit")
     * @ParamConverter("author")
     *
     * @param Request $request
     * @param Author $author
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Author $author)
    {
        $this->denyAccessUnlessGranted(Actions::EDIT, $author);

        return $this->processForm($request, $author, 'messages.changes_accepted');
    }

    /**
     * @Route("/authors/delete/{id}", name="authors_delete")
     * @ParamConverter("author")
     *
     * @param Author $author
     * @return RedirectResponse
     */
    public function deleteAction(Author $author)
    {
        $this->denyAccessUnlessGranted(Actions::DELETE, $author);

        $authorService = $this->get('app.authors');

        $translator = $this->get('translator');

        $authorService->remove($author);

        $this->addFlash('notice', $translator->trans('messages.author_deleted'));

        return $this->redirectToRoute('authors');
    }

    /**
     * @Route("/authors/subscribe/{id}", name="authors_subscribe")
     * @ParamConverter("author")
     *
     * @param Request $request
     * @param Author $author
     * @return JsonResponse|RedirectResponse
     */
    public function toggleSubscriptionAction(Request $request, Author $author)
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, $author);

        if (false === $request->isXmlHttpRequest()) {
            return $this->redirectToRoute('authors');
        }

        $authorService = $this->get('app.authors');

        return $this->json([
            'subscribed' => $authorService->toggleSubscription($author, $this->getUser())
        ]);
    }

    /**
     * @param Request $request
     * @param Author $author
     * @param string $message
     * @return RedirectResponse|Response
     */
    private function processForm(Request $request, Author $author, $message)
    {
        $authorService = $this->get('app.authors');

        $isNew = (null === $author->getId());

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $translator = $this->get('translator');

            $route = $isNew ? 'authors' : 'authors_edit';

            $authorService->save($author);

            $this->addFlash('notice', $translator->trans($message));

            return $this->redirectToRoute($route, [
                'id' => $author->getId()
            ]);
        }

        return $this->render('authors/form.html.twig', [
            'form' => $form->createView(),
            'author' => $author,
            'filterName' => $isNew ? null : Sessions::getFilterName(AuthorFilter::class)
        ]);
    }
}
