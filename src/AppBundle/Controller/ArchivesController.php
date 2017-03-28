<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


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
		$archiveService = $this->get('app.archives');

		$response = $archiveService->getArchive();

		if(false === $response instanceof BinaryFileResponse) {
			$translator = $this->get('translator');
			return $this->createNotFoundException($translator->trans('messages.file_not_found'));
		}

		return $response;
	}

	/**
	 * @Route("/archive/count", name="archive_count")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function countAction(Request $request)
	{
		if(false === $request->isXmlHttpRequest()) {
			return $this->redirectToRoute('books');
		}

		$archiveService = $this->get('app.archives');

		return $this->json([
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
