<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportsController extends Controller
{

	/**
	 * @Route("/export", name="export")
	 *
	 * @return Response
	 */
	public function indexAction()
	{
		return $this->render('exports/index.html.twig', []);
	}

	/**
	 * @Route("/export/authors", name="export_authors")
	 *
	 * @return BinaryFileResponse
	 */
	public function authorsExportAction()
	{
		$authorsService = $this->get('app.authors');

		$file = $authorsService->export();

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

		return $response;
	}

	/**
	 * @Route("/export/genres", name="export_genres")
	 *
	 * @return BinaryFileResponse
	 */
	public function genresExportAction()
	{
		$genresService = $this->get('app.genres');

		$file = $genresService->export();

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

		return $response;
	}

}
