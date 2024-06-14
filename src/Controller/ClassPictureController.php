<?php

namespace App\Controller;

use App\Entity\ClassPicture;
use App\Entity\Group;
use App\Entity\SectionContent;
use App\Entity\UserSectionContent;
use App\Form\ClassPictureSectionsType;
use App\Form\SectionContentTitleType;
use App\Form\SectionContentType;
use App\Form\UserSectionContentType;
use App\Repository\ClassPictureRepository;
use App\Repository\GroupRepository;
use App\Repository\ProfessorRepository;
use App\Repository\SectionContentRepository;
use App\Repository\StudentRepository;
use App\Repository\TemplateRepository;
use App\Repository\UserRepository;
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

    #[Route('/section-content/{id}/edit', name: 'section_content_edit')]
    public function editSectionContent(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sectionContent = $entityManager->getRepository(SectionContent::class)->find($id);

        if (!$sectionContent) {
            throw $this->createNotFoundException('SectionContent no encontrado');
        }

        $form = $this->createForm(SectionContentType::class, $sectionContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'SectionContent editado con éxito');
            return $this->redirectToRoute('class_picture_sections', ['id' => $sectionContent->getClassPicture()->getId()]);
        }

        return $this->render('class_picture/edit_section.html.twig', [
            'sectionContent' => $sectionContent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user-section-content/{id}/edit', name: 'user_section_content_edit')]
    public function editUserSectionContent(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $userSectionContent = $entityManager->getRepository(UserSectionContent::class)->find($id);

        if (!$userSectionContent) {
            throw $this->createNotFoundException('UserSectionContent no encontrado');
        }

        $form = $this->createForm(UserSectionContentType::class, $userSectionContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'UserSectionContent editado con éxito');
            return $this->redirectToRoute('section_content_edit', ['id' => $userSectionContent->getSectionContent()->getId()]);
        }

        return $this->render('class_picture/edit_user_section_content.html.twig', [
            'userSectionContent' => $userSectionContent,
            'form' => $form->createView(),
        ]);
    }
}