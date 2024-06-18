<?php

namespace App\Controller;

use App\Entity\UserPicture;
use App\Form\UserPictureType;
use App\Repository\UserPictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserPictureController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/userpictures/create', name: 'create_user_picture')]
    public function createUserPicture(
        UserPictureRepository $userPictureRepository,
        Request $request
    ): Response {
        $userPicture = new UserPicture();
        $form = $this->createForm(UserPictureType::class, $userPicture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageStream = fopen($imageFile->getRealPath(), 'rb');
                $userPicture->setImage(stream_get_contents($imageStream));
                fclose($imageStream);
            }

            // Asignar el usuario logueado a la entidad UserPicture
            $userPicture->setUser($this->getUser());

            try {
                $userPictureRepository->add($userPicture);
                $userPictureRepository->save();
                $this->addFlash('success', 'Se ha creado la imagen del usuario con éxito');

                // Redirigir al método de recorte de imagen
                return $this->redirectToRoute('app_cropperjs', ['id' => $userPicture->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear la imagen del usuario. Error: ' . $e->getMessage());
            }
        }

        return $this->render('user_picture/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}