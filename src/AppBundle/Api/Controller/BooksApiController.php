<?php

namespace AppBundle\Api\Controller;

use AppBundle\Filter\DTO\BookFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\Transformer\BookTransformer;

class BooksApiController extends Controller
{
    private const LIMIT = 50;

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
        $bookService = $this->get('app.books');
        $cacheService = $this->get('app.redis_cache');

        $query = $bookService->getFilteredBooks(new BookFilter(), $this->getUser());

        $page = $request->query->getInt('page', 1);

        $data = $cacheService->getData($query, new BookTransformer($this->get('router')), $page, self::LIMIT);

        return new JsonResponse($data);
    }
}
