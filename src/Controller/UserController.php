<?php

namespace App\Controller;

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

    #[Route('/users', name: 'modify_user')]
    final public function modifyUser(): Response
    {
        //implement methods on repositories
        return $this->render('users/list.html.twig');
    }

    #[Route('/users', name: 'delete_user')]
    final public function deleteUser(): Response
    {
        //implement methods on repositories
        return $this->render('users/list.html.twig');
    }
}