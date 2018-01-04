<?php

namespace AppBundle\Api\Controller;

use AppBundle\Entity\Genre;
use AppBundle\Filter\DTO\GenreFilter;
use AppBundle\Security\Actions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\Transformer\GenreTransformer;

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
    public function indexAction(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Genre());

        $genreService = $this->get('app.genres');
        $dataService = $this->get('app.load_api_data');

        $query = $genreService->getFilteredGenres(new GenreFilter());

        $page = $request->query->getInt('page', 1);

        $data = $dataService->loadData($query, new GenreTransformer(), $page, self::LIMIT);

        return new JsonResponse($data);
    }
}
