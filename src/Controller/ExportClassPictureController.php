<?php

namespace App\Controller;

use App\Repository\ClassPictureRepository;
use Sasedev\MpdfBundle\Factory\MpdfFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExportClassPictureController extends AbstractController
{
    #[Route('/class-picture/{id}/template-layout', name: 'class_picture_template_layout')]
    public function getClassPictureTemplateLayout(
        int $id,
        ClassPictureRepository $classPictureRepository
    ): Response
    {
        $classPicture = $classPictureRepository->find($id);

        if (!$classPicture || !$classPicture->getTemplate()) {
            throw $this->createNotFoundException('Orla o plantilla no encontrada');
        }

        $template = $classPicture->getTemplate();

        $callback = function () use ($template) {
            echo stream_get_contents($template->getLayout());
        };

        $response = new StreamedResponse($callback);
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }

    #[Route('/class-picture/{id}/export-pdf', name: 'class_picture_export_pdf')]
    public function exportClassPictureToPdf(
        int $id,
        ClassPictureRepository $classPictureRepository,
        MpdfFactory $mpdfFactory,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        // Obtiene la orla por su ID
        $classPicture = $classPictureRepository->find($id);

        if (!$classPicture) {
            throw $this->createNotFoundException('Orla no encontrada');
        }

        // Obtener la URL de la foto del template
        $templateImageUrl = $urlGenerator->generate(
            'class_picture_template_layout', [
            'id' => $classPicture->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Configura el objeto Mpdf
        $mpdf = $mpdfFactory->createMpdfObject([
            'mode' => 'utf-8',
            'format' => 'A3',
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => 'L' // Landscape orientation
        ]);

        // Genera el contenido HTML
        $htmlContent = $this->renderView('class_picture/class_picture_pdf.html.twig', [
            'classPicture' => $classPicture,
            'templateImageUrl' => $templateImageUrl
        ]);

        // Configura el contenido del PDF
        $mpdf->WriteHTML($htmlContent);

        // Devuelve el PDF como respuesta de descarga
        return $mpdfFactory->createDownloadResponse($mpdf, 'orla_' . $classPicture->getGroup() . '.pdf');
    }
}