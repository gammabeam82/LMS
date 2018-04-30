<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Transformer\BookTransformer;
use AppBundle\Entity\Book;
use AppBundle\Entity\File;
use AppBundle\Filter\DTO\BookFilter;
use AppBundle\Security\Actions;
use AppBundle\Service\Cache\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    public function listAction(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Book());

        $bookService = $this->get('app.books');
        $cacheService = $this->get('app.cache_service');

        $filter = new BookFilter();
        $options = new Options();

        $options->setQuery($bookService->getFilteredBooks($filter, $this->getUser()))
            ->setFilter($filter)
            ->setLimit(self::LIMIT)
            ->setTransformer(new BookTransformer($this->get('router')))
            ->setPage($request->query->getInt('page', 1));

        return new JsonResponse($cacheService->getData($options));
    }

    /**
     * @Route("/api/books/file/download/{id}", name="api_books_file_download")
     * @ParamConverter("file")
     *
     * @param File $file
     *
     * @return BinaryFileResponse
     */
    public function getFileAction(File $file): BinaryFileResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Book());

        $bookService = $this->get('app.books');
        $response = $bookService->downloadFile($file);

        return $response;
    }
}
