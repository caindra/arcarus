<?php

namespace App\Controller;

use App\Entity\ClassPicture;
use App\Entity\Group;
use App\Entity\SectionContent;
use App\Entity\UserSectionContent;
use App\Form\NoUsersSectionContentType;
use App\Form\SectionContentTitleType;
use App\Form\SectionContentType;
use App\Form\UserSectionContentType;
use App\Repository\AcademicYearRepository;
use App\Repository\ClassPictureRepository;
use App\Repository\GroupRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProfessorRepository;
use App\Repository\SectionContentRepository;
use App\Repository\StudentRepository;
use App\Repository\TemplateRepository;
use App\Repository\UserSectionContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassPictureController extends AbstractController
{
    #[Route('/class-pictures', name: 'class_pictures')]
    public function viewClassPictures(
        ClassPictureRepository $classPictureRepository,
        PaginatorInterface     $paginator,
        Request                $request
    ): Response
    {
        $query = $classPictureRepository->findAllWithGroupAndTemplate();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('class_picture/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/class-picture/select-group', name: 'class_picture_select_group')]
    public function selectGroupClassPicture(
        GroupRepository    $groupRepository,
        PaginatorInterface $paginator,
        Request            $request
    ): Response
    {
        $query = $groupRepository->findAll();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );
        return $this->render('class_picture/select_group.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/class-picture/select-template/{id}', name: 'class_picture_select_template')]
    public function selectTemplateClassPicture(
        Group              $group,
        TemplateRepository $templateRepository,
        PaginatorInterface $paginator,
        Request            $request
    ): Response
    {
        $query = $templateRepository->findAll();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('class_picture/select_template.html.twig', [
            'pagination' => $pagination,
            'group' => $group
        ]);
    }

    #[Route('/class-picture/create/{groupId}/{templateId}', name: 'class_picture_create')]
    public function createClassPicture(
        int                    $groupId,
        GroupRepository        $groupRepository,
        int                    $templateId,
        TemplateRepository     $templateRepository,
        ClassPictureRepository $classPictureRepository,
        Request                $request
    ): Response
    {
        $group = $groupRepository->find($groupId);
        $template = $templateRepository->find($templateId);
        $classPicture = new ClassPicture();

        if ($group && $template) {
            $classPicture->setGroup($group);
            $classPicture->setTemplate($template);
            $classPicture->setDescription('Orla de ' . $group->getName());

            foreach ($template->getSections() as $section) {
                $sectionContent = new SectionContent();
                $sectionContent->setSection($section);
                $classPicture->addSectionContent($sectionContent);
            }

            $form = $this->createFormBuilder($classPicture)
                ->add('sectionContents', CollectionType::class, [
                    'entry_type' => SectionContentTitleType::class,
                    'allow_add' => false,
                    'by_reference' => false,
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    foreach ($classPicture->getSectionContents() as $sectionContent) {
                        $sectionContent->setClassPicture($classPicture);
                    }
                    $classPictureRepository->save($classPicture);
                    $this->addFlash('success', 'Se ha creado la orla con éxito');
                    return $this->redirectToRoute('main');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'No se ha podido crear. Error: ' . $e->getMessage());
                }
            }

            return $this->render('class_picture/create.html.twig', [
                'form' => $form->createView(),
                'classPicture' => $classPicture
            ]);
        }

        return $this->render('class_picture/create.html.twig', [
            'classPicture' => $classPicture
        ]);
    }

    #[Route('/class-picture/{id}/sections', name: 'class_picture_sections')]
    public function showSections(int $id, ClassPictureRepository $classPictureRepository): Response
    {
        $classPicture = $classPictureRepository->find($id);

        if (!$classPicture) {
            throw $this->createNotFoundException('Orla no encontrada');
        }

        return $this->render('class_picture/show_sections.html.twig', [
            'classPicture' => $classPicture,
            'sections' => $classPicture->getSectionContents(),
        ]);
    }

    #[Route('class-picture/section/{id}/options', name: 'section_options')]
    public function sectionOptions(
        int $id,
        Request $request,
        SectionContentRepository $sectionContentRepository
    ): Response
    {
        $sectionContent = $sectionContentRepository->find($id);

        if (!$sectionContent) {
            throw $this->createNotFoundException('Sección no encontrada');
        }

        $formSubmitted = $request->isMethod('POST');

        if ($formSubmitted) {
            $hasUsers = $request->request->get('has_users');
            if ($hasUsers === 'yes') {
                $userType = $request->request->get('user_type');
                switch ($userType) {
                    case 'students':
                        return $this->redirectToRoute('students_page', [
                            'id' => $id
                        ]);
                    case 'professors':
                        return $this->redirectToRoute('professors_page', [
                            'id' => $id
                        ]);
                    case 'all':
                        return $this->redirectToRoute('all_users_page', [
                            'id' => $id
                        ]);

                }
            } else {
                return $this->redirectToRoute('no_users_page', [
                    'id' => $id
                ]);
            }
        }

        return $this->render('class_picture/section-options.html.twig', [
            'sectionContent' => $sectionContent,
        ]);
    }

    #[Route('/class-picture/section/{id}/students', name: 'students_page')]
    public function studentsPage(
        int $id,
        SectionContentRepository $sectionContentRepository,
        StudentRepository $studentRepository,
        UserSectionContentRepository $userSectionContentRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sectionContent = $sectionContentRepository->find($id);

        if (!$sectionContent) {
            throw $this->createNotFoundException('Sección no encontrada');
        }

        $classPicture = $sectionContent->getClassPicture();
        $group = $classPicture->getGroup();

        $students = $studentRepository->findByGroup($group);
        $formBuilder = $this->createFormBuilder();

        $userSectionContents = [];
        foreach ($students as $student) {
            $userSectionContent = new UserSectionContent();
            $userSectionContent->addContainedUser($student);
            $entityManager->persist($userSectionContent);

            $formBuilder->add('userSectionContent_' . $student->getId(), UserSectionContentType::class, [
                'data' => $userSectionContent,
            ]);

            $userSectionContents[$student->getId()] = $userSectionContent;
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($userSectionContents as $userSectionContent) {
                    $userSectionContentRepository->add($userSectionContent);
                    $sectionContent->addUserContent($userSectionContent);
                }

                $userSectionContentRepository->save();
                $sectionContentRepository->save();

                $this->addFlash('success', 'Usuarios agregados a la sección con éxito.');
                return $this->redirectToRoute('class_pictures');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido modificar el contenido de la seccion. Error: ' . $e->getMessage());
            }
        }

        return $this->render('class_picture/section-options-student.html.twig', [
            'sectionContent' => $sectionContent,
            'students' => $students,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class-picture/section/{id}/professors', name: 'professors_page')]
    public function professorsPage(
        int $id,
        SectionContentRepository $sectionContentRepository,
        ProfessorRepository $professorRepository,
        UserSectionContentRepository $userSectionContentRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sectionContent = $sectionContentRepository->find($id);

        if (!$sectionContent) {
            throw $this->createNotFoundException('Sección no encontrada');
        }

        $classPicture = $sectionContent->getClassPicture();
        $group = $classPicture->getGroup();

        $professors = $professorRepository->findByGroup($group);
        $formBuilder = $this->createFormBuilder();

        $userSectionContents = [];
        foreach ($professors as $professor) {
            $userSectionContent = new UserSectionContent();
            $userSectionContent->addContainedUser($professor);
            $entityManager->persist($userSectionContent);

            $formBuilder->add('userSectionContent_' . $professor->getId(), UserSectionContentType::class, [
                'data' => $userSectionContent,
            ]);

            $userSectionContents[$professor->getId()] = $userSectionContent;
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($userSectionContents as $userSectionContent) {
                    $userSectionContentRepository->add($userSectionContent);
                    $sectionContent->addUserContent($userSectionContent);
                }

                $userSectionContentRepository->save();
                $sectionContentRepository->save();

                $this->addFlash('success', 'Usuarios agregados a la sección con éxito.');
                return $this->redirectToRoute('class_pictures');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido modificar el contenido de la sección. Error: ' . $e->getMessage());
            }
        }

        return $this->render('class_picture/section-options-professor.html.twig', [
            'sectionContent' => $sectionContent,
            'professors' => $professors,
            'form' => $form->createView(),
        ]);
    }

    #[Route('class-picture/section/{id}/all-users', name: 'all_users_page')]
    public function allUsersPage(
        int $id,
        SectionContentRepository $sectionContentRepository,
        StudentRepository $studentRepository,
        ProfessorRepository $professorRepository,
        UserSectionContentRepository $userSectionContentRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sectionContent = $sectionContentRepository->find($id);

        if (!$sectionContent) {
            throw $this->createNotFoundException('Sección no encontrada');
        }

        $classPicture = $sectionContent->getClassPicture();
        $group = $classPicture->getGroup();

        $students = $studentRepository->findByGroup($group);
        $professors = $professorRepository->findByGroup($group);

        // Unir estudiantes y profesores en un solo array
        $allUsers = array_merge($students, $professors);

        // Crear el formulario principal
        $formBuilder = $this->createFormBuilder();

        $userSectionContents = [];
        foreach ($allUsers as $user) {
            $userSectionContent = new UserSectionContent();
            $userSectionContent->addContainedUser($user);
            $entityManager->persist($userSectionContent);

            $formBuilder->add('userSectionContent_' . $user->getId(), UserSectionContentType::class, [
                'data' => $userSectionContent,
            ]);

            $userSectionContents[$user->getId()] = $userSectionContent;
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($userSectionContents as $userSectionContent) {
                    $userSectionContentRepository->add($userSectionContent);
                    $sectionContent->addUserContent($userSectionContent);
                }

                $userSectionContentRepository->save();
                $sectionContentRepository->save();

                $this->addFlash('success', 'Usuarios agregados a la sección con éxito.');
                return $this->redirectToRoute('all_users_page', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido modificar el contenido de la sección. Error: ' . $e->getMessage());
            }
        }

        return $this->render('class_picture/section-options-all-users.html.twig', [
            'sectionContent' => $sectionContent,
            'allUsers' => $allUsers,
            'form' => $form->createView(),
        ]);
    }

    #[Route('class-picture/section/{id}/no-users', name: 'no_users_page')]
    public function noUsersPage(
        int $id,
        SectionContentRepository $sectionContentRepository,
        Request $request
    ): Response
    {
        $sectionContent = $sectionContentRepository->find($id);

        if (!$sectionContent) {
            throw $this->createNotFoundException('Sección no encontrada');
        }

        // Obtener el grupo, el año académico y la organización relacionados
        $group = $sectionContent->getClassPicture()->getGroup();
        $academicYear = $group->getAcademicYear();
        $organization = $group->getOrganization();

        // Crear el formulario
        $form = $this->createForm(NoUsersSectionContentType::class, null, [
            'group' => $group,
            'academic_year' => $academicYear,
            'organization' => $organization,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Procesar los datos del formulario
            $data = $form->getData();
            $selectedOptions = $data['options'];

            $title = $sectionContent->getTitle();
            foreach ($selectedOptions as $option) {
                switch ($option) {
                    case 'group_name':
                        $title = $group->getName();
                        break;
                    case 'academic_year_description':
                        $title = $academicYear->getDescription();
                        break;
                    case 'organization_name':
                        $title = $organization->getName();
                        break;
                }
            }

            $this->addFlash('success', 'Sección actualizada con éxito.');
            return $this->redirectToRoute('class_pictures', ['id' => $id]);
        }

        return $this->render('class_picture/no-users.html.twig', [
            'sectionContent' => $sectionContent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class-picture/delete/{id}', name: 'class_picture_delete')]
    public function deleteClassPicture(
        ClassPicture $classPicture,
        ClassPictureRepository $classPictureRepository,
        SectionContentRepository $sectionContentRepository,
        UserSectionContentRepository $userSectionContentRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                // Eliminar dependencias de UserSectionContent y User
                foreach ($classPicture->getSectionContents() as $sectionContent) {
                    foreach ($sectionContent->getUserContents() as $userContent) {
                        // Eliminar User que depende de UserSectionContent
                        foreach ($userContent->getContainedUsers() as $user) {
                            $entityManager->remove($user);
                        }
                        $entityManager->remove($userContent);
                    }
                    $entityManager->remove($sectionContent);
                }

                // Eliminar ClassPicture
                $entityManager->remove($classPicture);
                $entityManager->flush();

                $this->addFlash('success', 'La orla ha sido eliminada con éxito');
                return $this->redirectToRoute('class_pictures');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar la orla. Error: ' . $e->getMessage());
            }
        }

        return $this->render('class_picture/delete.html.twig', [
            'classPicture' => $classPicture
        ]);
    }
}