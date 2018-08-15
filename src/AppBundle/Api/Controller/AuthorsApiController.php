<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Request\Author\CreateAuthorRequest;
use AppBundle\Api\Request\Author\UpdateAuthorRequest;
use AppBundle\Api\Transformer\AuthorTransformer;
use AppBundle\Entity\Author;
use AppBundle\Filter\DTO\AuthorFilter;
use AppBundle\Security\Actions;
use AppBundle\Service\Cache\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthorsApiController extends Controller
{
    private const LIMIT = 50;

    /**
     * @param Author $author
     * @param string $statusCode
     *
     * @return JsonResponse
     */
    private function response(Author $author, string $statusCode): JsonResponse
    {
        $transformer = new AuthorTransformer();

        return new JsonResponse($transformer->transform($author), $statusCode);
    }

    /**
     * @Route("/api/authors", name="api_authors")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request): JsonResponse
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

        return new JsonResponse($cacheService->getData($options), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/authors", name="api_authors_add")
     * @Method({"POST"})
     *
     * @param CreateAuthorRequest $dto
     *
     * @return JsonResponse
     */
    public function addAction(CreateAuthorRequest $dto): JsonResponse
    {
        $author = Author::createFromDTO($dto);

        $this->denyAccessUnlessGranted(Actions::CREATE, $author);

        $this->get('app.authors')->save($author);
        $this->get('app.cache_service')->clearCache();

        return $this->response($author, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/authors/{id}", name="api_authors_show")
     * @Method({"GET"})
     * @ParamConverter("genre")
     *
     * @param Author $author
     *
     * @return JsonResponse
     */
    public function showAction(Author $author): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, $author);

        return $this->response($author, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/authors/{id}", name="api_authors_update")
     * @Method({"PUT"})
     * @ParamConverter("author")
     *
     * @param UpdateAuthorRequest $dto
     * @param Author $author
     *
     * @return JsonResponse
     */
    public function updateAction(UpdateAuthorRequest $dto, Author $author): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::EDIT, $author);

        $author->update($dto);

        $this->get('app.authors')->save($author);
        $this->get('app.cache_service')->clearCache();

        return $this->response($author, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/authors/{id}", name="api_authors_delete")
     * @Method({"DELETE"})
     * @ParamConverter("author")
     *
     * @param Author $author
     *
     * @return JsonResponse
     */
    public function deleteAction(Author $author): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::DELETE, $author);

        $this->get('app.authors')->remove($author);
        $this->get('app.cache_service')->clearCache();

        return $this->response($author, JsonResponse::HTTP_OK);
    }
}
