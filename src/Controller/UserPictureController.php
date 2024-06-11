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
            $userPicture->setUser($this->security->getUser());

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

    #[Route('/userpictures/modify/{id}', name: 'modify_user_picture')]
    public function modifyUserPicture(
        Request $request,
        UserPictureRepository $userPictureRepository,
        UserPicture $userPicture
    ): Response {
        $form = $this->createForm(UserPictureType::class, $userPicture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageStream = fopen($imageFile->getRealPath(), 'rb');
                $userPicture->setImage(stream_get_contents($imageStream));
                fclose($imageStream);
            }

            // Asignar el usuario logueado a la entidad UserPicture si no está ya asignado
            if (!$userPicture->getUser()) {
                $userPicture->setUser($this->security->getUser());
            }

            try {
                $userPictureRepository->save();
                $this->addFlash('success', 'La modificación de la imagen se ha realizado correctamente');
                return $this->redirectToRoute('user_pictures');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error: ' . $e->getMessage());
            }
        }

        return $this->render('user_picture/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/userpictures/delete/{id}', name: 'delete_user_picture')]
    public function deleteUserPicture(
        UserPicture $userPicture,
        UserPictureRepository $userPictureRepository,
        Request $request
    ): Response {
        if ($request->request->has('confirmar')) {
            try {
                $userPictureRepository->remove($userPicture);
                $userPictureRepository->save();
                $this->addFlash('success', 'La imagen del usuario ha sido eliminada con éxito');
                return $this->redirectToRoute('user_pictures');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar la imagen del usuario. Error: ' . $e->getMessage());
            }
        }

        return $this->render('user_picture/delete.html.twig', [
            'userPicture' => $userPicture
        ]);
    }
}