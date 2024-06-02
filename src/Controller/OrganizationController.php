<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Form\OrganizationType;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrganizationController extends AbstractController
{
    #[Route('/organizations', name: 'organizations')]
    final public function listOrganizations(
        EntityManagerInterface $entityManager,
        OrganizationRepository $organizationRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $organizationRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );
        return $this->render('general/organization/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/organizations/create', name: 'organization_create')]
    final public function createOrganization(
        OrganizationRepository $organizationRepository,
        Request $request,
    ): Response
    {
        $organization = new Organization();
        $organizationRepository->add($organization);
        $form = $this->createForm(OrganizationType::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $organizationRepository->add($organization);
                $organizationRepository->save();
                $this->addFlash('success', 'Se ha creado con éxito');
                return $this->redirectToRoute('organizations');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear. Error: ' . $e->getMessage());
            }
        }

        return $this->render('general/organization/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/organizations/modify/{id}', name: 'organization_edit')]
    public function modifyOrganization(
        Request $request,
        Organization $organization,
        OrganizationRepository $organizationRepository
    ): Response {
        $form = $this->createForm(OrganizationType::class, $organization);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $organizationRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('organizations');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error: ' . $e->getMessage());
            }
        }
        return $this->render('general/organization/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/organizations/delete/{id}', name: 'organization_delete')]
    final public function deleteOrganization(
        Organization $organization,
        OrganizationRepository $organizationRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                $organizationRepository->remove($organization);
                $organizationRepository->save();
                $this->addFlash('success', 'El curso académico ha sido eliminado con éxito');
                return $this->redirectToRoute('organizations');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar el curso académico. Error: ' . $e);
            }
        }

        return $this->render('general/organization/delete.html.twig', [
            'organization' => $organization
        ]);
    }
}