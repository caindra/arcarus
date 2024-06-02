<?php

namespace App\Controller;

use App\Entity\SectionContent;
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

    #[Route('/section-contents/modify/{id}', name: 'section_content_edit')]
    public function modifySectionContent(
        Request $request,
        SectionContent $sectionContent,
        SectionContentRepository $sectionContentRepository,
    ): Response {
        $form = $this->createForm(SectionContentType::class, $sectionContent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $sectionContentRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('section_contents');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error: ' . $e->getMessage());
            }
        }
        return $this->render('general/section_content/modify.html.twig', [
            'form' => $form->createView()
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