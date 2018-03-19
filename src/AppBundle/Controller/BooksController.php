<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\File;
use AppBundle\Event\BookEvent;
use AppBundle\Events;
use AppBundle\Filter\DTO\BookFilter;
use AppBundle\Filter\Form\BookFilterType;
use AppBundle\Form\BookType;
use AppBundle\Security\Actions;
use AppBundle\Service\Cache\Options;
use AppBundle\Service\Sessions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class BooksController extends Controller
{
    private const LIMIT = 15;

    /**
     * @Route("/books", name="books")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, new Book());

        $serviceFacade = $this->get('app.book_service_facade');

        $filter = new BookFilter();

        $filterForm = $this->createForm(BookFilterType::class, $filter);
        $filterForm->handleRequest($request);

        try {
            $serviceFacade->sessionService()->updateFilterFromSession($filterForm, $filter);
        } catch (UnexpectedValueException $e) {
            $translator = $this->get('translator');
            $this->addFlash('error', $translator->trans($e->getMessage()));
        }

        if (null !== $request->get('reset') || null !== $request->get('id')) {
            return $this->redirectToRoute("books");
        }

        $options = new Options();

        $options->setQuery($serviceFacade->bookService()->getFilteredBooks($filter, $this->getUser()))
            ->setFilter($filter)
            ->setLimit(self::LIMIT)
            ->setPage($request->query->getInt('page', 1));

        return $this->render('books/index.html.twig', [
            'form' => $filterForm->createView(),
            'books' => $serviceFacade->cacheService()->getData($options),
            'booksInArchive' => $serviceFacade->archiveService()->getBookIds(),
            'filterName' => $serviceFacade->sessionService()->getFilterName($filter)
        ]);
    }

    /**
     * @Route("/books/add", name="books_add")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $book = new Book();

        $this->denyAccessUnlessGranted(Actions::CREATE, $book);

        return $this->processForm($request, $book, 'messages.book_added');
    }

    /**
     * @Route("/books/edit/{id}", name="books_edit")
     * @ParamConverter("book")
     *
     * @param Request $request
     * @param Book $book
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Book $book)
    {
        $this->denyAccessUnlessGranted(Actions::EDIT, $book);

        return $this->processForm($request, $book, 'messages.changes_accepted');
    }

    /**
     * @Route("/books/delete/{id}", name="books_delete")
     * @ParamConverter("book")
     *
     * @param Book $book
     *
     * @return RedirectResponse
     */
    public function deleteAction(Book $book)
    {
        $this->denyAccessUnlessGranted(Actions::DELETE, $book);

        $translator = $this->get('translator');

        $this->get('app.archives')->removeBookFromArchive($book);

        $this->get('app.books')->remove($book);

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(Events::BOOK_DELETED, new BookEvent($book));

        $this->addFlash('notice', $translator->trans('messages.book_deleted'));

        return $this->redirectToRoute('books');
    }

    /**
     * @Route("/books/file/delete/{id}", name="books_file_delete")
     * @ParamConverter("file")
     *
     * @param File $file
     *
     * @return RedirectResponse
     */
    public function deleteBookFileAction(File $file)
    {
        $this->denyAccessUnlessGranted(Actions::DELETE, $file->getBook());

        $bookService = $this->get('app.books');

        $translator = $this->get('translator');

        $bookService->removeFile($file);

        $this->addFlash('notice', $translator->trans('messages.changes_accepted'));

        return $this->redirectToRoute('books_edit', [
            'id' => $file->getBook()->getId()
        ]);
    }

    /**
     * @Route("/books/file/download/{id}", name="books_file_download")
     * @ParamConverter("file")
     *
     * @param Request $request
     * @param File $file
     *
     * @return BinaryFileResponse
     */
    public function getFileAction(Request $request, File $file)
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, $file->getBook());

        $bookService = $this->get('app.books');

        try {
            $response = $bookService->downloadFile($file, $request->query->getInt('thumbnail', 0));
        } catch (\LogicException $e) {
            $translator = $this->get('translator');
            throw $this->createNotFoundException($translator->trans('messages.file_not_found'));
        }

        return $response;
    }

    /**
     * @Route("/books/view/{id}", name="books_view")
     * @ParamConverter("book")
     *
     * @param Book $book
     *
     * @return Response
     */
    public function viewAction(Book $book)
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, $book);

        $bookService = $this->get('app.books');

        return $this->render('books/view.html.twig', [
            'book' => $book,
            'images' => $bookService->getImages($book)
        ]);
    }

    /**
     * @Route("/books/like/{id}", name="books_like")
     * @ParamConverter("book")
     *
     * @param Request $request
     * @param Book $book
     *
     * @return JsonResponse|RedirectResponse
     */
    public function toggleLikeAction(Request $request, Book $book)
    {
        $this->denyAccessUnlessGranted(Actions::VIEW, $book);

        if (false === $request->isXmlHttpRequest()) {
            return $this->redirectToRoute('books');
        }

        $bookService = $this->get('app.books');

        return $this->json([
            'hasLike' => $bookService->toggleLike($this->getUser(), $book)
        ]);
    }

    /**
     * @param Request $request
     * @param Book $book
     * @param string $message
     *
     * @return RedirectResponse|Response
     */
    private function processForm(Request $request, Book $book, string $message)
    {
        $bookService = $this->get('app.books');

        $isNew = (null === $book->getId());

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $translator = $this->get('translator');

            $route = $isNew ? 'books' : 'books_edit';

            try {
                $bookService->save($this->getUser(), $book);

                if (false !== $isNew) {
                    $dispatcher = $this->get('event_dispatcher');
                    $dispatcher->dispatch(Events::BOOK_CREATED, new BookEvent($book));
                }

                $this->addFlash('notice', $translator->trans($message));
                return $this->redirectToRoute($route, [
                    'id' => $book->getId()
                ]);
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('messages.upload_error'));
            }
        }

        return $this->render('books/form.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
            'filterName' => $isNew ? null : Sessions::getFilterName(BookFilter::class)
        ]);
    }
}
