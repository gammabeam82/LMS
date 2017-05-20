<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ExportItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Service\Genres;
use AppBundle\Service\Authors;
use AppBundle\Service\Series;

class ExportsController extends Controller
{

	/**
	 * @Route("/export", name="export")
	 *
	 * @return Response
	 */
	public function indexAction()
	{
		$exportService = $this->get('app.export');

		return $this->render('exports/index.html.twig', [
			'exports' => $exportService->getExportsList()
		]);
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
	 * @Route("/export/series", name="export_series")
	 *
	 * @return BinaryFileResponse
	 */
	public function seriesExportAction()
	{
		return $this->processExport($this->get('app.series'));
	}

	/**
	 * @Route("/export/purge", name="export_purge")
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function purgeAction()
	{
		$translator = $this->get('translator');

		$exportService = $this->get('app.export');

		$exportService->purge();

		$this->addFlash('notice', $translator->trans('messages.purged'));

		return $this->redirectToRoute('export');
	}

	/**
	 * @Route("/export/download/{id}", name="export_download")
	 * @ParamConverter("item")
	 *
	 * @param ExportItem $item
	 * @return BinaryFileResponse
	 */
	public function downloadAction(ExportItem $item)
	{
		$response = new BinaryFileResponse($item->getFilename());
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

		return $response;
	}

	/**
	 * @Route("/export/delete/{id}", name="export_delete")
	 * @ParamConverter("item")
	 *
	 * @param ExportItem $item
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction(ExportItem $item)
	{
		$translator = $this->get('translator');

		$exportService = $this->get('app.export');

		$exportService->remove($item);

		$this->addFlash('notice', $translator->trans('messages.export_deleted'));

		return $this->redirectToRoute('export');
	}

	/**
	 * @param $service
	 * @return BinaryFileResponse
	 */
	private function processExport($service)
	{
		/**
		 * @var Genres | Authors | Series $service
		 */
		$file = $service->export();

		$response = new BinaryFileResponse($file);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

		return $response;
	}

}
