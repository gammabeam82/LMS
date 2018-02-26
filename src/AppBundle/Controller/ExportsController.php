<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\ExportItem;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Serie;
use AppBundle\Security\Actions;
use AppBundle\Service\Export\ExportInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportsController extends Controller
{

    /**
     * @Route("/export", name="export")
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function indexAction(): Response
    {
        $exportService = $this->get('app.export');

        return $this->render('exports/index.html.twig', [
            'exports' => $exportService->getExportsList(),
            'itemsCount' => $exportService->getItemsCount()
        ]);
    }

    /**
     * @Route("/export/authors", name="export_authors")
     *
     * @return BinaryFileResponse
     */
    public function authorsExportAction(): BinaryFileResponse
    {
        $this->denyAccessUnlessGranted(Actions::EXPORT, new Author());

        return $this->processExport($this->get('app.authors'));
    }

    /**
     * @Route("/export/genres", name="export_genres")
     *
     * @return BinaryFileResponse
     */
    public function genresExportAction(): BinaryFileResponse
    {
        $this->denyAccessUnlessGranted(Actions::EXPORT, new Genre());

        return $this->processExport($this->get('app.genres'));
    }

    /**
     * @Route("/export/series", name="export_series")
     *
     * @return BinaryFileResponse
     */
    public function seriesExportAction(): BinaryFileResponse
    {
        $this->denyAccessUnlessGranted(Actions::EXPORT, new Serie());

        return $this->processExport($this->get('app.series'));
    }

    /**
     * @Route("/export/purge", name="export_purge")
     *
     * @return RedirectResponse
     */
    public function purgeAction(): RedirectResponse
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
    public function downloadAction(ExportItem $item): BinaryFileResponse
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
     * @return RedirectResponse
     */
    public function deleteAction(ExportItem $item): RedirectResponse
    {
        $translator = $this->get('translator');

        $exportService = $this->get('app.export');
        $exportService->remove($item);

        $this->addFlash('notice', $translator->trans('messages.export_deleted'));

        return $this->redirectToRoute('export');
    }

    /**
     * @param ExportInterface $service
     * @return BinaryFileResponse
     */
    private function processExport(ExportInterface $service): BinaryFileResponse
    {
        $file = $service->export();

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}
