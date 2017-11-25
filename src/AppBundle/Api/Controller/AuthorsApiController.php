<?php

namespace AppBundle\Api\Controller;

use AppBundle\Filter\DTO\AuthorFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\Transformer\AuthorTransformer;

class AuthorsApiController extends Controller
{
    private const LIMIT = 50;

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
        $authorService = $this->get('app.authors');
        $dataService = $this->get('app.load_api_data');

        $query = $authorService->getFilteredAuthors(new AuthorFilter());

        $page = $request->query->getInt('page', 1);

        $data = $dataService->getData($query, new AuthorTransformer(), $page, self::LIMIT);

        return new JsonResponse($data);
    }
}
