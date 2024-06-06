<?php

namespace App\Controller;

use App\Entity\SectionContent;
use App\Entity\UserSectionContent;
use App\Form\SectionContentType;
use App\Repository\SectionContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SectionContentController extends AbstractController
{
    #[Route('/section-contents', name: 'section_contents')]
    final public function listSectionContents(
        EntityManagerInterface $entityManager,
        SectionContentRepository $sectionContentRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $sectionContentRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );
        return $this->render('general/section_content/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/section-contents/create', name: 'section_content_create')]
    final public function createSection(
        SectionContentRepository $sectionContentRepository,
        Request $request,
    ): Response
    {
        $sectionContent = new SectionContent();
        $sectionContentRepository->add($sectionContent);
        $form = $this->createForm(SectionContentType::class, $sectionContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $sectionContentRepository->add($sectionContent);
                $sectionContentRepository->save();
                $this->addFlash('success', 'Se ha creado con éxito');
                return $this->redirectToRoute('section_contents');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/section_content/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/section-content/{id}/edit', name: 'section_content_edit')]
    public function edit(
        SectionContent $sectionContent,
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {
        $form = $this->createForm(SectionContentType::class, $sectionContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($sectionContent->getUserContents() as $userContent) {
                if ($userContent->getOrderNumber() === 0) {
                    $sectionContent->removeUserContent($userContent);
                }
            }
            $entityManager->flush();
            return $this->redirectToRoute('section_content_success');
        }

        $allUsers = $userRepository->findAll(); // Adaptar a tu lógica de negocio
        foreach ($sectionContent->getUserContents() as $userContent) {
            $user = $userContent->getContainedUsers();
            if ($allUsers->contains($user)) {
                $allUsers->removeElement($user);
            }
        }

        foreach ($allUsers as $user) {
            $newUserContent = new UserSectionContent();
            $newUserContent->setSectionContent($user);
            $newUserContent->setSectionContent($sectionContent);
            $newUserContent->setOrderNumber(0);
            $sectionContent->addUserContent($newUserContent);
        }

        return $this->render('section_content/edit.html.twig', [
            'sectionContentForm' => $form->createView(),
        ]);
    }

    #[Route('/section-contents/delete/{id}', name: 'section_content_delete')]
    final public function deleteSectionContent(
        SectionContent $sectionContent,
        SectionContentRepository $sectionContentRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                $sectionContentRepository->remove($sectionContent);
                $sectionContentRepository->save();
                $this->addFlash('success', 'El curso académico ha sido eliminado con éxito');
                return $this->redirectToRoute('section_contents');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar el curso académico. Error: ' . $e);
            }
        }

        return $this->render('general/section_content/delete.html.twig', [
            'group' => $sectionContent
        ]);
    }
}