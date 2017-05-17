<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Service\Genres;
use AppBundle\Service\Authors;

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
		return $this->processExport($this->get('app.authors'));
	}

	/**
	 * @Route("/export/genres", name="export_genres")
	 *
	 * @return BinaryFileResponse
	 */
	public function genresExportAction()
	{
		return $this->processExport($this->get('app.genres'));
	}

	/**
	 * @param $service
	 * @return BinaryFileResponse
	 */
	private function processExport($service)
	{
		/**
		 * @var Genres | Authors $service
		 */
		$file = $service->export();

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

		return $response;
	}

}
