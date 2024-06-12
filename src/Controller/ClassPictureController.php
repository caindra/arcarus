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
use App\Repository\TemplateRepository;
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
        PaginatorInterface $paginator,
        Request $request
    ): Response {
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
        GroupRepository $groupRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
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
        Group $group,
        TemplateRepository $templateRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
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
        int $groupId,
        GroupRepository $groupRepository,
        int $templateId,
        TemplateRepository $templateRepository,
        ClassPictureRepository $classPictureRepository,
        Request $request
    ): Response {
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
                foreach ($classPicture->getSectionContents() as $sectionContent) {
                    $sectionContent->setClassPicture($classPicture);
                }
                $classPictureRepository->save($classPicture);
                $this->addFlash('success', 'Se ha creado la orla con éxito');
                return $this->redirectToRoute('main');
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

    #[Route('/class-picture/edit-section/{id}', name: 'class_picture_edit_section')]
    public function editSectionContent(
        SectionContent $sectionContent,
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $group = $sectionContent->getClassPicture()->getGroup();
        $users = $userRepository->findByGroup($group);

        // Eliminar usuarios ya asociados
        foreach ($sectionContent->getUserContents() as $userContent) {
            if (($key = array_search($userContent->getContainedUsers(), $users)) !== false) {
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

        $form = $this->createForm(SectionContentType::class, $sectionContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($sectionContent->getUserContents() as $userContent) {
                if ($userContent->getOrderNumber() === 0) {
                    $sectionContent->removeUserContent($userContent);
                    $entityManager->remove($userContent);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Sección actualizada con éxito');
            return $this->redirectToRoute('class_picture_edit_section', ['id' => $sectionContent->getId()]);
        }

        return $this->render('class_picture/edit_section.html.twig', [
            'form' => $form->createView(),
            'sectionContent' => $sectionContent,
        ]);
    }
}