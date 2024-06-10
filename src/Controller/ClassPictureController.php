<?php

namespace App\Controller;

use App\Entity\ClassPicture;
use App\Entity\Group;
use App\Entity\SectionContent;
use App\Entity\Template;
use App\Repository\ClassPictureRepository;
use App\Repository\GroupRepository;
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
    final public function viewClassPictures(
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

    #[Route('/class-picture/select-group', name: 'class_picture_select_group')]
    final public function selectGroupClassPicture(
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
        return $this->render('class_picture/select_group.html.twig', [
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

    #[Route('/class-picture/create/{groupId}/{templateId}', name: 'class_picture_create')]
    final public function createClassPicture(
        Group $groupId,
        GroupRepository $groupRepository,
        Template $templateId,
        TemplateRepository $templateRepository,
        ClassPictureRepository $classPictureRepository,
        Request $request,
    ): Response
    {
        $group = $groupRepository->find($groupId);
        $template = $templateRepository->find($templateId);
        $classPicture = new ClassPicture();

        if ($group && $template) {
            try{
                $classPicture->setGroup($group);
                $classPicture->setTemplate($template);
                $classPicture->setDescription('Orla de ' . $group->getName());

                foreach ($template->getSections() as $section) {
                    $sectionContent = new SectionContent();
                    $sectionContent->setClassPicture($classPicture);
                    $sectionContent->setSection($section);
                    $classPicture->addSectionContent($sectionContent);
                }

                $classPictureRepository->add($classPicture);
                $classPictureRepository->save();
                $this->addFlash('sucess', 'Se ha creado la orla con éxito');
                return $this->redirectToRoute('main');
            }catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido crear. Error: ' . $e->getMessage());
                return $this->redirectToRoute('main');
            }
        }

        return $this->render('class_picture/create.html.twig', [
            'classPicture' => $classPicture
        ]);
    }
}