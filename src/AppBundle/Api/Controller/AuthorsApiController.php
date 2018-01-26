<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Service\Options;
use AppBundle\Api\Transformer\AuthorTransformer;
use AppBundle\Entity\Author;
use AppBundle\Filter\DTO\AuthorFilter;
use AppBundle\Security\Actions;
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
        $dataService = $this->get('app.load_api_data');

        $options = new Options();

        $options->setQuery($authorService->getFilteredAuthors(new AuthorFilter()))
            ->setTransformer(new AuthorTransformer())
            ->setPage($request->query->getInt('page', 1))
            ->setLimit(self::LIMIT);

        $data = $dataService->loadData($options);

        return new JsonResponse($data);
    }
}
