<?php

namespace AppBundle\Api\Controller;

use AppBundle\Filter\DTO\AuthorFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\Transformer\AuthorTransformer;
use AppBundle\Api\Provider\ApiDataProvider;

class AuthorsApiController extends Controller
{
    private const LIMIT = 30;

    /**
     * @Route("/api/authors", name="api_authors")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request): JsonResponse
    {
        $paginator = $this->get('knp_paginator');

        $authorService = $this->get('app.authors');

        $filter = new AuthorFilter();

        $query = $authorService->getFilteredAuthors($filter);

        $authors = $paginator->paginate(
            $query, $request->query->getInt('page', 1), self::LIMIT
        );

        $provider = new ApiDataProvider([
            'items' => $authors,
            'transformer' => new AuthorTransformer()
        ]);

        return new JsonResponse($provider->getData());
    }
}
