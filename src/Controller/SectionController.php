<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\SectionType;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    #[Route('/sections', name: 'sections')]
    final public function listSections(
        EntityManagerInterface $entityManager,
        SectionRepository $sectionRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $sectionRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );
        return $this->render('general/section/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/sections/create', name: 'section_create')]
    final public function createSection(
        SectionRepository $sectionRepository,
        Request $request,
    ): Response
    {
        $section = new Section();
        $sectionRepository->add($section);
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $sectionRepository->add($section);
                $sectionRepository->save();
                $this->addFlash('success', 'Se ha creado con éxito');
                return $this->redirectToRoute('sections');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/section/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/sections/modify/{id}', name: 'section_edit')]
    public function modifySection(
        Request $request,
        Section $section,
        SectionRepository $sectionRepository,
    ): Response {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $sectionRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('sections');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error: ' . $e->getMessage());
            }
        }
        return $this->render('general/section/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/sections/delete/{id}', name: 'section_delete')]
    final public function deleteSection(
        Section $section,
        SectionRepository $sectionRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                // Eliminar los contenidos de la sección manualmente
                foreach ($section->getSectionContents() as $sectionContent) {
                    $entityManager->remove($sectionContent);
                }
                $entityManager->flush();

                $sectionRepository->remove($section);
                $sectionRepository->save();
                $this->addFlash('success', 'La sección ha sido eliminada con éxito');
                return $this->redirectToRoute('sections');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar la sección. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/section/delete.html.twig', [
            'group' => $section
        ]);
    }

}