<?php

namespace App\Controller;

use App\Entity\SectionContent;
use App\Entity\UserSectionContent;
use App\Form\SectionContentType;
use App\Repository\GroupRepository;
use App\Repository\SectionContentRepository;
use App\Repository\UserRepository;
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
        SectionContentRepository $sectionContentRepository,
        UserRepository $userRepository,
        GroupRepository $groupRepository
    ): Response {
        $form = $this->createForm(SectionContentType::class, $sectionContent);
        $form->handleRequest($request);

        // Obtener todos los usuarios del grupo asociado al ClassPicture
        $group = $sectionContent->getClassPicture()->getGroup();
        $allUsers = array_merge(
            $groupRepository->findStudentsByGroupId($group->getId()),
            $groupRepository->findProfessorsByGroupId($group->getId())
        );

        // Filtrar los usuarios que ya están en userContents
        foreach ($sectionContent->getUserContents() as $userContent) {
            $user = $userContent->getContainedUsers()->first(); // Suponiendo que hay un solo usuario por UserSectionContent
            foreach ($allUsers as $key => $groupUser) {
                if ($groupUser['id'] === $user->getId()) {
                    unset($allUsers[$key]);
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Eliminar UserSectionContent con orderNumber 0
                foreach ($sectionContent->getUserContents() as $userContent) {
                    if ($userContent->getOrderNumber() === 0) {
                        $sectionContent->removeUserContent($userContent);
                    }
                }

                // Añadir los usuarios restantes a la sección con orderNumber 0
                foreach ($allUsers as $groupUser) {
                    $newUserContent = new UserSectionContent();
                    $user = $userRepository->find($groupUser->getId());
                    $newUserContent->addContainedUser($user);
                    $newUserContent->setSectionContent($sectionContent);
                    $newUserContent->setOrderNumber(0);
                    $sectionContent->addUserContent($newUserContent);
                }

                $sectionContentRepository->save();

                return $this->redirectToRoute('section_contents');
            }catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/section_content/modify.html.twig', [
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

    #[Route('/section-content/edit/{id}', name: 'section_content_class_picture')]
    public function editSectionContent(
        SectionContent $sectionContent,
        SectionContentRepository $sectionContentRepository,
        Request $request,
        GroupRepository $groupRepository
    ): Response {
        $group = $sectionContent->getClassPicture()->getGroup();
        $users = array_merge(
            $groupRepository->findProfessorsByGroupId($group),
            $groupRepository->findStudentsByGroupId($group)
        );

        $form = $this->createForm(SectionContentType::class, $sectionContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($sectionContent->getUserContents() as $userContent) {
                if ($userContent->getOrderNumber() === 0) {
                    $sectionContent->removeUserContent($userContent);
                }
            }
            $sectionContentRepository->save();

            $this->addFlash('success', 'Section content updated successfully');
            return $this->redirectToRoute('main');
        }

        return $this->render('class_picture/edit_section_content.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}