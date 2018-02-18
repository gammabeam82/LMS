<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Transformer\AuthorTransformer;
use AppBundle\Entity\Author;
use AppBundle\Filter\DTO\AuthorFilter;
use AppBundle\Security\Actions;
use AppBundle\Service\Cache\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $this->denyAccessUnlessGranted(Actions::VIEW, new Author());

        $authorService = $this->get('app.authors');
        $cacheService = $this->get('app.cache_service');

        $filter = new AuthorFilter();
        $options = new Options();

        $options->setQuery($authorService->getFilteredAuthors($filter))
            ->setFilter($filter)
            ->setLimit(self::LIMIT)
            ->setTransformer(new AuthorTransformer())
            ->setPage($request->query->getInt('page', 1));

        return new JsonResponse($cacheService->getData($options));
    }
}
