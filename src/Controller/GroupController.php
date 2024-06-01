<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    #[Route('/groups', name: 'groups')]
    final public function listTemplates(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $groupRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );
        return $this->render('general/group/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/groups/create', name: 'group_create')]
    final public function createAcademicYear(
        GroupRepository $groupRepository,
        Request $request,
    ): Response
    {
        $group = new Group();
        $groupRepository->add($group);
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $groupRepository->add($group);
                $groupRepository->save();
                $this->addFlash('success', 'Se ha creado con éxito');
                return $this->redirectToRoute('groups');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/group/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/groups/modify/{id}', name: 'group_edit')]
    public function modifyAcademicYear(
        Request $request,
        Group $group,
        GroupRepository $groupRepository,
    ): Response {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $groupRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('groups');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error: ' . $e->getMessage());
            }
        }
        return $this->render('general/group/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/groups/delete/{id}', name: 'group_delete')]
    final public function deleteAcademicYear(
        Group $group,
        GroupRepository $groupRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                $groupRepository->remove($group);
                $groupRepository->save();
                $this->addFlash('success', 'El curso académico ha sido eliminado con éxito');
                return $this->redirectToRoute('groups');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar el curso académico. Error: ' . $e);
            }
        }

        return $this->render('general/group/delete.html.twig', [
            'group' => $group
        ]);
    }
}