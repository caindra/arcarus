<?php

namespace App\Controller;

use App\Entity\AcademicYear;
use App\Form\AcademicYearType;
use App\Repository\AcademicYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcademicYearController extends AbstractController
{
    #[Route('/academic-years', name: 'academic-years')]
    final public function listAcademicYears(
        EntityManagerInterface $entityManager,
        AcademicYearRepository $academicYearRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $academicYearRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );
        return $this->render('general/academic_year/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/academic-years/create', name: 'academic_year_create')]
    final public function createAcademicYear(
        AcademicYearRepository $academicYearRepository,
        Request $request,
    ): Response
    {
        $academicYear = new AcademicYear();
        $academicYearRepository->add($academicYear);
        $form = $this->createForm(AcademicYearType::class, $academicYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $academicYearRepository->add($academicYear);
                $academicYearRepository->save();
                $this->addFlash('success', 'Se ha creado el curso académico con éxito');
                return $this->redirectToRoute('academic-years');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear el curso académico. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/academic_year/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/academic-years/modify/{id}', name: 'academic_year_edit')]
    public function modifyAcademicYear(
        Request $request,
        AcademicYear $academicYear,
        AcademicYearRepository $academicYearRepository,
    ): Response {
        $form = $this->createForm(AcademicYearType::class, $academicYear);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $academicYearRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('academic-years');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error: ' . $e->getMessage());
            }
        }
        return $this->render('general/academic_year/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/academic-years/delete/{id}', name: 'academic_year_delete')]
    final public function deleteAcademicYear(
        AcademicYear $academicYear,
        AcademicYearRepository $academicYearRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $academicYearRepository->remove($academicYear);
                $academicYearRepository->save();
                $this->addFlash('success', 'El curso académico ha sido eliminado con éxito');
                return $this->redirectToRoute('academic-years');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar el curso académico. Error: ' . $e);
            }
        }

        return $this->render('general/academic_year/delete.html.twig', [
            'academicYear' => $academicYear
        ]);
    }
}