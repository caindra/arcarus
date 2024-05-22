<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'users')]
    final public function listUsers(): Response
    {
        //implement methods on repositories
        return $this->render('users/list.html.twig');
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