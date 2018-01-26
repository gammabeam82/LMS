<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Service\Options;
use AppBundle\Api\Transformer\GenreTransformer;
use AppBundle\Entity\Genre;
use AppBundle\Filter\DTO\GenreFilter;
use AppBundle\Security\Actions;
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
    public function indexAction(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Genre());

        $genreService = $this->get('app.genres');
        $dataService = $this->get('app.load_api_data');

        $options = new Options();

        $options->setQuery($genreService->getFilteredGenres(new GenreFilter()))
            ->setTransformer(new GenreTransformer())
            ->setPage($request->query->getInt('page', 1))
            ->setLimit(self::LIMIT);

        $data = $dataService->loadData($options);

        return new JsonResponse($data);
    }
}
