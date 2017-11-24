<?php

namespace AppBundle\Api\Controller;

use AppBundle\Filter\DTO\BookFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\Transformer\BookTransformer;
use AppBundle\Api\Provider\ApiDataProvider;

class BooksApiController extends Controller
{
    private const LIMIT = 30;

    /**
     * @Route("/api/books", name="api_books")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request): JsonResponse
    {
        $paginator = $this->get('knp_paginator');

        $bookService = $this->get('app.books');

        $filter = new BookFilter();

        $query = $bookService->getFilteredBooks($filter, $this->getUser());

        $books = $paginator->paginate(
            $query, $request->query->getInt('page', 1), self::LIMIT
        );

        $provider = new ApiDataProvider([
            'items' => $books,
            'transformer' => new BookTransformer($this->get('router'))
        ]);

        return new JsonResponse($provider->getData());
    }
}
