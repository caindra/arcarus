<?php

namespace App\Controller;

use App\Repository\UserPictureRepository;
use App\Service\UxPackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Cropperjs\Factory\CropperInterface;
use Symfony\UX\Cropperjs\Form\CropperType;

class CropperjsController extends AbstractController
{
    public function __construct(
        private Packages $assets,
        private string $projectDir,
    ) {
    }

    #[Route('/cropperjs/{id}', name: 'app_cropperjs')]
    public function __invoke(UserPictureRepository $userPictureRepository, CropperInterface $cropper, Request $request, int $id): Response
    {
        $userPicture = $userPictureRepository->find($id);

        if (!$userPicture) {
            throw $this->createNotFoundException('Imagen de usuario no encontrada');
        }

        $imagePath = tempnam(sys_get_temp_dir(), 'crop');
        file_put_contents($imagePath, $userPicture->getImage());

        $crop = $cropper->createCrop($imagePath);
        $crop->setCroppedMaxSize(1000, 750);

        $form = $this->createFormBuilder(['crop' => $crop])
            ->add('crop', CropperType::class, [
                'public_url' => 'data:image/jpeg;base64,' . base64_encode($userPicture->getImage()),
                'cropper_options' => [
                    'aspectRatio' => 4 / 3,
                    'preview' => '#cropper-preview',
                    'scalable' => false,
                    'zoomable' => false,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        $croppedImage = null;
        $croppedThumbnail = null;
        if ($form->isSubmitted()) {
            // faking an error to let the page re-render with the cropped images
            $form->addError(new FormError('🤩'));
            $croppedImage = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedImage()));
            $croppedThumbnail = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedThumbnail(200, 150)));
        }

        return $this->render('ux_packages/cropperjs.html.twig', [
            'form' => $form->createView(),
            'croppedImage' => $croppedImage,
            'croppedThumbnail' => $croppedThumbnail,
        ]);
    }
}