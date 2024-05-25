<?php

namespace App\Controller;

use App\Entity\Professor;
use App\Entity\Student;
use App\Form\ProfessorType;
use App\Form\StudentType;
use App\Repository\ProfessorRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'users')]
    final public function listUsers(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $userRepository->findAllBySurnameName();
        $pagination = $paginator->paginate(
            $query, // query, NOT result
            $request->query->getInt('page', 1), // page number
            15 // limit per page
        );
        return $this->render('users/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/users', name: 'add_user')]
    final public function addUser(): Response
    {
        //implement methods on repositories
        return $this->render('users/list.html.twig');
    }

    #[Route('/users/modify/student/{id}', name: 'modify_student')]
    final public function modifyStudent(
        Request $request,
        StudentRepository $studentRepository,
        Student $student
    ): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $studentRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('users');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se han podido aplicar las modificaciones');
            }
        }
        return $this->render('users/modify_student.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/users/modify/professor/{id}', name: 'modify_professor')]
    final public function modifyProfessor(
        Request $request,
        ProfessorRepository $professorRepository,
        Professor $professor
    ): Response
    {
        $form = $this->createForm(ProfessorType::class, $professor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $professorRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('users');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se han podido aplicar las modificaciones');
            }
        }
        return $this->render('users/modify_professor.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/users/delete/student/{id}', name: 'delete_student')]
    final public function deleteStudent(
        Student $student,
        StudentRepository $studentRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $studentRepository->remove($student);
                $studentRepository->save();
                $this->addFlash('success', 'El estudiante ha sido eliminado con éxito');
                return $this->redirectToRoute('users');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar al estudiante. Error: ' . $e);
            }
        }

        return $this->render('users/delete_student.html.twig', [
            'user' => $student
        ]);
    }

    #[Route('/users/delete/professor/{id}', name: 'delete_professor')]
    final public function deleteProfessor(
        Professor $professor,
        ProfessorRepository $professorRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $professorRepository->remove($professor);
                $professorRepository->save();
                $this->addFlash('success', 'El profesor ha sido eliminado con éxito');
                return $this->redirectToRoute('users');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar al profesor. Error: ' . $e);
            }
        }

        return $this->render('users/delete_professor.html.twig', [
            'user' => $professor
        ]);
    }
}