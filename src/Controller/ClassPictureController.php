<?php

namespace App\Controller;

use App\Entity\ClassPicture;
use App\Entity\Group;
use App\Entity\SectionContent;
use App\Entity\UserSectionContent;
use App\Form\SectionContentTitleType;
use App\Form\SectionContentType;
use App\Repository\ClassPictureRepository;
use App\Repository\GroupRepository;
use App\Repository\ProfessorRepository;
use App\Repository\StudentRepository;
use App\Repository\TemplateRepository;
use App\Repository\UserSectionContentRepository;
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

    #[Route('/class-picture/edit-sections/{classPictureId}', name: 'class_picture_edit_sections')]
    public function editSectionContents(
        int $classPictureId,
        ClassPictureRepository $classPictureRepository,
        StudentRepository $studentRepository,
        ProfessorRepository $professorRepository,
        UserSectionContentRepository $userSectionContentRepository,
        Request $request
    ): Response {
        $classPicture = $classPictureRepository->find($classPictureId);
        if (!$classPicture) {
            throw $this->createNotFoundException('No se encontró la orla solicitada.');
        }

        $group = $classPicture->getGroup();
        if (!$group) {
            $this->addFlash('error', 'No se encontró el grupo asociado a la orla.');
            return $this->redirectToRoute('class_pictures');
        }

        $students = $studentRepository->findByGroup($group);
        $professors = $professorRepository->findByGroup($group);

        $users = array_merge($students, $professors);

        $sectionContents = $classPicture->getSectionContents();
        foreach ($sectionContents as $sectionContent) {
            // Eliminar usuarios ya asociados
            foreach ($sectionContent->getUserContents() as $userContent) {
                $user = $userContent->getContainedUsers()->first();
                if (($key = array_search($user, $users)) !== false) {
                    unset($users[$key]);
                }
            }

            // Añadir nuevos UserSectionContent con orderNumber 0
            foreach ($users as $user) {
                $userSectionContent = new UserSectionContent();
                $userSectionContent->addContainedUser($user);
                $userSectionContent->setSectionContent($sectionContent);
                $userSectionContent->setOrderNumber(0);
                $sectionContent->addUserContent($userSectionContent);
            }
        }

        // Crear el formulario para todos los SectionContent
        $forms = [];
        foreach ($sectionContents as $sectionContent) {
            $form = $this->createForm(SectionContentType::class, $sectionContent);
            $forms[] = $form;
            $form->handleRequest($request);
        }

        if ($request->isMethod('POST')) {
            $allFormsValid = true;
            foreach ($forms as $form) {
                if (!$form->isSubmitted() || !$form->isValid()) {
                    $allFormsValid = false;
                    break;
                }
            }

            if ($allFormsValid) {
                try {
                    foreach ($sectionContents as $sectionContent) {
                        foreach ($sectionContent->getUserContents() as $userContent) {
                            if ($userContent->getOrderNumber() === 0) {
                                $sectionContent->removeUserContent($userContent);
                                $userSectionContentRepository->remove($userContent);
                            }
                        }
                    }

                    $classPictureRepository->save();
                    $userSectionContentRepository->save();
                    $this->addFlash('success', 'Secciones actualizadas con éxito');
                    return $this->redirectToRoute('class_picture_edit_sections', ['classPictureId' => $classPictureId]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'No se ha podido editar. Error: ' . $e->getMessage());
                }
            }
        }

        return $this->render('class_picture/edit_section.html.twig', [
            'forms' => $forms,
            'sectionContents' => $sectionContents,
        ]);
    }
}