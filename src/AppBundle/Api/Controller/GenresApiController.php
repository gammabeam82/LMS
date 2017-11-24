<?php

namespace AppBundle\Api\Controller;

use AppBundle\Filter\DTO\GenreFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\Transformer\GenreTransformer;
use AppBundle\Api\Provider\ApiDataProvider;

class GenresApiController extends Controller
{
    private const LIMIT = 30;

    /**
     * @Route("/api/genres", name="api_genres")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request): JsonResponse
    {
        $paginator = $this->get('knp_paginator');

        $genreService = $this->get('app.genres');

        $filter = new GenreFilter();

        $query = $genreService->getFilteredGenres($filter);

        $genres = $paginator->paginate(
            $query, $request->query->getInt('page', 1), self::LIMIT
        );

        $provider = new ApiDataProvider([
            'items' => $genres,
            'transformer' => new GenreTransformer()
        ]);

        return new JsonResponse($provider->getData());
    }
}
