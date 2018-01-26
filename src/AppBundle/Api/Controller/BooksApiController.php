<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Service\Options;
use AppBundle\Api\Transformer\BookTransformer;
use AppBundle\Entity\Book;
use AppBundle\Entity\File;
use AppBundle\Filter\DTO\BookFilter;
use AppBundle\Security\Actions;
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
    public function indexAction(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Book());

        $bookService = $this->get('app.books');
        $dataService = $this->get('app.load_api_data');

        $transformer = new BookTransformer($this->get('router'));

        $options = new Options();

        $options->setQuery($bookService->getFilteredBooks(new BookFilter()))
            ->setTransformer($transformer)
            ->setPage($request->query->getInt('page', 1))
            ->setLimit(self::LIMIT);

        $data = $dataService->loadData($options);

        return new JsonResponse($data);
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
