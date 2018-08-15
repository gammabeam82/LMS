<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Request\Genre\CreateGenreRequest;
use AppBundle\Api\Request\Genre\UpdateGenreRequest;
use AppBundle\Api\Transformer\GenreTransformer;
use AppBundle\Entity\Genre;
use AppBundle\Filter\DTO\GenreFilter;
use AppBundle\Security\Actions;
use AppBundle\Service\Cache\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GenresApiController extends Controller
{
    private const LIMIT = 50;

    /**
     * @param Genre $genre
     * @param string $statusCode
     *
     * @return JsonResponse
     */
    private function response(Genre $genre, string $statusCode): JsonResponse
    {
        $transformer = new GenreTransformer();

        return new JsonResponse($transformer->transform($genre), $statusCode);
    }

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
     * @Route("/api/genres", name="api_genres_add")
     * @Method({"POST"})
     *
     * @param CreateGenreRequest $dto
     *
     * @return JsonResponse
     */
    public function addAction(CreateGenreRequest $dto): JsonResponse
    {
        $genre = Genre::createFromDTO($dto);

        $this->denyAccessUnlessGranted(Actions::CREATE, $genre);

        $this->get('app.genres')->save($genre);
        $this->get('app.cache_service')->clearCache();

        return $this->response($genre, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_show")
     * @Method({"GET"})
     * @ParamConverter("genre")
     *
     * @param Genre $genre
     *
     * @return JsonResponse
     */
    public function showAction(Genre $genre): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, $genre);

        return $this->response($genre, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_update")
     * @Method({"PUT"})
     * @ParamConverter("genre")
     *
     * @param UpdateGenreRequest $dto
     * @param Genre $genre
     *
     * @return JsonResponse
     */
    public function updateAction(UpdateGenreRequest $dto, Genre $genre): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::EDIT, $genre);

        $genre->update($dto);

        $this->get('app.genres')->save($genre);
        $this->get('app.cache_service')->clearCache();

        return $this->response($genre, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_delete")
     * @Method({"DELETE"})
     * @ParamConverter("genre")
     *
     * @param Genre $genre
     *
     * @return JsonResponse
     */
    public function deleteAction(Genre $genre): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::DELETE, $genre);

        $this->get('app.genres')->remove($genre);
        $this->get('app.cache_service')->clearCache();

        return $this->response($genre, JsonResponse::HTTP_OK);
    }
}
