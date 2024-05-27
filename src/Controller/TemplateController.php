<?php

namespace App\Controller;

use App\Entity\Template;
use App\Form\NewTemplateType;
use App\Form\TemplateType;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/templates', name: 'templates')]
    final public function listUsers(
        EntityManagerInterface $entityManager,
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
        return $this->render('users/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/templates/create', name: 'create_template')]
    final public function addStudent(
        TemplateRepository $templateRepository,
        Request $request,
    ): Response
    {
        $template = new Template();
        $templateRepository->add($template);
        $form = $this->createForm(NewTemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $templateRepository->save();
                $this->addFlash('success', 'Se ha creado la plantilla con éxito');
                return $this->redirectToRoute('templates');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido crear la plantilla. Error: ' . $e);
            }
        }

        return $this->render('template/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/templates/modify/{id}', name: 'modify_template')]
    final public function modifyStudent(
        Request $request,
        TemplateRepository $templateRepository,
        Template $template
    ): Response
    {
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $templateRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('templates');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se han podido aplicar las modificaciones');
            }
        }
        return $this->render('template/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/templates/delete/{id}', name: 'delete_student')]
    final public function deleteStudent(
        Template $template,
        TemplateRepository $templateRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $templateRepository->remove($template);
                $templateRepository->save();
                $this->addFlash('success', 'La plantilla ha sido eliminado con éxito');
                return $this->redirectToRoute('templates');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar la plantilla. Error: ' . $e);
            }
        }

        return $this->render('template/delete.html.twig', [
            'user' => $template
        ]);
    }
}