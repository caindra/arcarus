<?php

namespace App\Controller;

use App\Entity\ClassPicture;
use App\Entity\SectionContent;
use App\Repository\ClassPictureRepository;
use Sasedev\MpdfBundle\Factory\MpdfFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExportClassPictureController extends AbstractController
{
    private $mpdfFactory;

    public function __construct(MpdfFactory $mpdfFactory)
    {
        $this->mpdfFactory = $mpdfFactory;
    }

    #[Route('/class-picture/{id}/choose-export', name: 'class_picture_choose_export')]
    public function chooseExportOption(int $id): Response
    {
        return $this->render('class_picture/choose_export.html.twig', [
            'id' => $id
        ]);
    }

    #[Route('/class-picture/{id}/handle-export', name: 'class_picture_handle_export')]
    public function handleExport(
        int $id,
        Request $request,
        ClassPictureRepository $classPictureRepository,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $exportOption = $request->request->get('export_option');
        $classPicture = $classPictureRepository->find($id);

        if (!$classPicture) {
            throw $this->createNotFoundException('Orla no encontrada');
        }

        if ($exportOption === 'template') {
            return $this->exportWithTemplate($classPicture, $urlGenerator);
        } elseif ($exportOption === 'custom_pdf') {
            $customPdfFile = $request->files->get('custom_pdf_file');
            if ($customPdfFile) {
                $customPdfPath = $this->uploadFile($customPdfFile);
                return $this->exportWithCustomPdf($classPicture, $customPdfPath);
            } else {
                $this->addFlash('error', 'No se subió ningún archivo PDF.');
                return $this->redirectToRoute('class_picture_choose_export', ['id' => $id]);
            }
        }
        return new Response('Opción no válida', 400);
    }

    private function exportWithTemplate(ClassPicture $classPicture, UrlGeneratorInterface $urlGenerator): Response
    {
        $template = $classPicture->getTemplate();
        if (!$template) {
            throw $this->createNotFoundException('Template no encontrado para la orla.');
        }

        $templateImagePath = $this->saveTemplateImageFromDatabase($template->getLayout(), 'template_' . $classPicture->getId() . '.png');

        $mpdf = $this->mpdfFactory->createMpdfObject([
            'mode' => 'utf-8',
            'format' => 'A3',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => 'L'
        ]);

        // Añadir detalles de la organización, año académico y grupo
        $organizationName = $template->getOrganization()->getName();
        $academicYearDescription = $classPicture->getGroup()->getAcademicYear()->getDescription();
        $groupName = $classPicture->getGroup()->getName();

        $headerHtml = "
            <div style='text-align: center; margin-bottom: 20px; margin-top: 50px;'>
                <h1>$organizationName</h1>
                <h2>$academicYearDescription</h2>
                <h3>$groupName</h3>
            </div>
        ";

        // Generar el contenido de las secciones
        $sectionContents = $classPicture->getSectionContents();
        $contentHtml = '';

        foreach ($sectionContents as $sectionContent) {
            $contentHtml .= $this->generateSectionContent($sectionContent);
        }

        // Combinar el header y el contenido en un solo HTML
        $fullHtml = $headerHtml . $contentHtml;

        $mpdf->AddPage();
        $mpdf->Image($templateImagePath, 0, 0, 420, 297, 'png', '', true, false);
        $mpdf->WriteFixedPosHTML($fullHtml, 20, 30, 380, 270);  // Ajuste de los márgenes izquierdo y derecho

        return new Response($mpdf->Output('orla_' . $classPicture->getGroup()->getName() . '.pdf', 'D'), 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    private function exportWithCustomPdf(ClassPicture $classPicture, string $customPdfPath): Response
    {
        $mpdf = $this->mpdfFactory->createMpdfObject([
            'mode' => 'utf-8',
            'format' => 'A3',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => 'L'
        ]);

        $pagecount = $mpdf->setSourceFile($customPdfPath);
        $tplId = $mpdf->importPage($pagecount);
        $mpdf->useTemplate($tplId, 0, 0, 420, 297);

        // Añadir detalles de la organización, año académico y grupo
        $organizationName = $classPicture->getTemplate()->getOrganization()->getName();
        $academicYearDescription = $classPicture->getGroup()->getAcademicYear()->getDescription();
        $groupName = $classPicture->getGroup()->getName();

        $headerHtml = "
            <div style='text-align: center; margin-bottom: 20px; margin-top: 50px;'>
                <h1>$organizationName</h1>
                <h2>$academicYearDescription</h2>
                <h3>$groupName</h3>
            </div>
        ";

        // Generar el contenido de las secciones
        $sectionContents = $classPicture->getSectionContents();
        $contentHtml = '';

        foreach ($sectionContents as $sectionContent) {
            $contentHtml .= $this->generateSectionContent($sectionContent);
        }

        // Combinar el header y el contenido en un solo HTML
        $fullHtml = $headerHtml . $contentHtml;

        $mpdf->WriteFixedPosHTML($fullHtml, 20, 30, 380, 270);  // Ajuste de los márgenes izquierdo y derecho

        return new Response($mpdf->Output('orla_' . $classPicture->getGroup()->getName() . '.pdf', 'D'), 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    private function generateSectionContent(SectionContent $sectionContent): string
    {
        $userContents = $sectionContent->getUserContents();
        $users = [];

        foreach ($userContents as $userContent) {
            $containedUsers = $userContent->getContainedUsers();
            foreach ($containedUsers as $user) {
                $userPicture = $user->getPicture();
                $imageUrl = $this->getParameter('kernel.project_dir') . '/public/images/user/user_default.png'; // Default image path

                if ($userPicture) {
                    $imageData = stream_get_contents($userPicture->getImage());
                    $imageName = 'user_' . $user->getId() . '.jpg';
                    $imageUrl = $this->saveImageFromDatabase($imageData, $imageName);
                }

                $users[] = [
                    'name' => $user->getName() . ' ' . $user->getSurnames(),
                    'imageUrl' => $imageUrl,
                    'description' => $userContent->getDescription()
                ];
            }
        }

        $maxColQuantity = $sectionContent->getSection()->getMaxColQuantity();
        $htmlContent = '<table style="width: 100%; margin-left: 20px; margin-right: 20px;">'; // Añadir márgenes
        $numUsers = count($users);
        $rows = ceil($numUsers / $maxColQuantity);

        for ($row = 0; $row < $rows; $row++) {
            $htmlContent .= '<tr>';
            $colsInRow = ($row == $rows - 1 && $numUsers % $maxColQuantity != 0) ? $numUsers % $maxColQuantity : $maxColQuantity;
            $paddingCols = floor(($maxColQuantity - $colsInRow) / 2);
            if ($paddingCols > 0) {
                $htmlContent .= str_repeat('<td style="padding: 10px;"></td>', $paddingCols);
            }

            for ($col = 0; $col < $colsInRow; $col++) {
                $index = $row * $maxColQuantity + $col;
                if ($index < $numUsers) {
                    $user = $users[$index];
                    $htmlContent .= '<td style="padding: 10px; text-align: center;">';
                    $htmlContent .= '<div style="border: 1px solid #ccc; padding: 10px; width: 100%;">';
                    $htmlContent .= '<img src="' . $user['imageUrl'] . '" alt="Foto" style="width:100px;height:100px;">';
                    $htmlContent .= '<p>' . $user['name'] . '</p>';
                    if ($user['description']) {
                        $htmlContent .= '<p>' . $user['description'] . '</p>';
                    }
                    $htmlContent .= '</div>';
                    $htmlContent .= '</td>';
                }
            }

            if ($paddingCols > 0) {
                $htmlContent .= str_repeat('<td style="padding: 10px;"></td>', $paddingCols);
            }
            $htmlContent .= '</tr>';
        }

        $htmlContent .= '</table>';
        return $htmlContent;
    }

    private function saveTemplateImageFromDatabase($imageData, string $imageName): string
    {
        $tempDir = sys_get_temp_dir();
        $imagePath = $tempDir . DIRECTORY_SEPARATOR . $imageName;

        file_put_contents($imagePath, stream_get_contents($imageData));

        return $imagePath;
    }

    private function saveImageFromDatabase($imageData, string $imageName): string
    {
        $tempDir = sys_get_temp_dir();
        $imagePath = $tempDir . DIRECTORY_SEPARATOR . $imageName;

        file_put_contents($imagePath, $imageData);

        return $imagePath;
    }

    private function uploadFile($file): string
    {
        $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($uploadsDirectory, $fileName);

        return $uploadsDirectory . '/' . $fileName;
    }
}