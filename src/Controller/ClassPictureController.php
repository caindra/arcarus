<?php

namespace App\Controller;

use App\Entity\ClassPicture;
use App\Entity\Group;
use App\Entity\Template;
use App\Repository\ClassPictureRepository;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassPictureController extends AbstractController
{
    #[Route('/class-pictures', name: 'class_pictures')]
    final public function listAcademicYears(
        EntityManagerInterface $entityManager,
        ClassPictureRepository $classPictureRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $classPictureRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );

        return $this->render('class_picture/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/class-picture/select-template/{id}', name: 'class_picture_select_template')]
    final public function selectTemplateClassPicture(
        //el id que se pasa es el de group, ya que es necesario para un controlador posterior
        Group $group,
        TemplateRepository $templateRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $templateRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );
        return $this->render('class_picture/select_template.html.twig', [
            'pagination' => $pagination,
            'group' => $group
        ]);
    }

    #[Route('/class-picture/create/{group_id}/{template_id}', name: 'class_picture_create')]
    final public function createClassPicture(
        Group $group,
        Template $template,
        Request $request,
    ): Response
    {
        $classPicture = new ClassPicture();

        // se asigna tanto el grupo como la plantilla a la orla
        $classPicture->setGroup($group);
        $classPicture->setTemplate($template);
        $classPicture->setDescription('Orla de la clase de ' . $group->getName());


        return $this->render('general/class_picture/create.html.twig', [
            'classPicture' => $classPicture
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
        GroupRepository $groupRepository, // Asegúrate de inyectar el repositorio de Group
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                // Eliminar todos los grupos asociados al curso académico
                foreach ($academicYear->getGroups() as $group) {
                    $groupRepository->remove($group);
                }
                $academicYearRepository->save();

                // Ahora eliminar el curso académico
                $academicYearRepository->remove($academicYear);
                $academicYearRepository->save();

                $this->addFlash('success', 'El curso académico ha sido eliminado con éxito');
                return $this->redirectToRoute('academic-years');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar el curso académico. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/academic_year/delete.html.twig', [
            'academicYear' => $academicYear
        ]);
    }
}