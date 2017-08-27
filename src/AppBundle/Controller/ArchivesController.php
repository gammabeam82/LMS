<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use LengthException;

class ArchivesController extends Controller
{

	/**
	 * @Route("/archive", name="archive_index")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction()
	{
		$archiveService = $this->get('app.archives');

		return $this->render('archives/index.html.twig', [
			'books' => $archiveService->getBooksList()
		]);
	}

	/**
	 * @Route("/archive/download", name="archive_download")
	 *
	 * @return BinaryFileResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function downloadAction()
	{
		$this->denyAccessUnlessGranted('view', new Book());

		$archiveService = $this->get('app.archives');

		try {
			$response = $archiveService->getArchive();
		} catch (LengthException $e) {
			$translator = $this->get('translator');
			throw $this->createNotFoundException($translator->trans('messages.file_not_found'));
		}

		return $response;
	}

	/**
	 * @Route("/archive/count", name="archive_count")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function countAction()
	{
		$archiveService = $this->get('app.archives');

		return $this->render('archives/count.html.twig', [
			'booksCount' => $archiveService->getBooksCount()
		]);
	}

	/**
	 * @Route("/archive/add/{id}", name="archive_add")
	 * @ParamConverter("book")
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function addAction(Request $request, Book $book)
	{
		$this->denyAccessUnlessGranted('view', $book);

		if(false === $request->isXmlHttpRequest()) {
			return $this->redirectToRoute('books');
		}

		$archiveService = $this->get('app.archives');

		$translator = $this->get('translator');

		$archiveService->addBookToArchive($book);

		return $this->json([
			'booksCount' => $archiveService->getBooksCount(),
			'message' => $translator->trans('messages.book_archive_added')
		]);
	}

	/**
	 * @Route("/archive/remove/{id}", name="archive_remove")
	 * @ParamConverter("book")
	 *
	 * @param Request $request
	 * @param Book $book
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function removeAction(Request $request, Book $book)
	{
		$this->denyAccessUnlessGranted('view', $book);

		if(false === $request->isXmlHttpRequest()) {
			return $this->redirectToRoute('books');
		}

		$archiveService = $this->get('app.archives');

		$translator = $this->get('translator');

		$archiveService->removeBookFromArchive($book);

		return $this->json([
			'booksCount' => $archiveService->getBooksCount(),
			'message' => $translator->trans('messages.book_archive_removed')
		]);
	}
}
