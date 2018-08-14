<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Request\Genre\CreateGenreRequest;
use AppBundle\Api\Transformer\GenreTransformer;
use AppBundle\Entity\Genre;
use AppBundle\Filter\DTO\GenreFilter;
use AppBundle\Security\Actions;
use AppBundle\Service\Cache\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GenresApiController extends Controller
{
    private const LIMIT = 50;

    /**
     * @Route("/api/genres", name="api_genres")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Genre());

        $genreService = $this->get('app.genres');
        $cacheService = $this->get('app.cache_service');

        $filter = new GenreFilter();
        $options = new Options();

        $options->setQuery($genreService->getFilteredGenres($filter))
            ->setFilter($filter)
            ->setLimit(self::LIMIT)
            ->setTransformer(new GenreTransformer())
            ->setPage($request->query->getInt('page', 1));

        return new JsonResponse($cacheService->getData($options), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/genres/add", name="api_genres_add")
     * @Method({"POST"})
     *
     * @param CreateGenreRequest $dto
     *
     * @return JsonResponse
     */
    public function addAction(CreateGenreRequest $dto): JsonResponse
    {
        $genre = Genre::createFromDTO($dto);

        $genreService = $this->get('app.genres');
        $genreService->save($genre);

        return new JsonResponse((new GenreTransformer())->transform($genre), JsonResponse::HTTP_CREATED);
    }
}
