<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeUserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils) : Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Este mensaje no debería de aparecer');
    }

    #[Route('/change-password', name: 'app_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangeUserPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->hashPassword($user, $form->get('newPassword')->getData())
            );
            $userRepository->save();
            $this->addFlash('success', 'Contraseña cambiada correctamente');
            return $this->redirectToRoute('main');
        }
        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
            'admin' => false,
        ]);
    }

    #[Route('/login/change-password', name: 'app_change_password_login')]
    public function changePasswordLogin(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangeUserPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->hashPassword($user, $form->get('newPassword')->getData())
            );
            $userRepository->save();
            $this->addFlash('success', 'Contraseña cambiada correctamente');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/own_password.html.twig', [
            'form' => $form->createView(),
            'admin' => false,
        ]);
    }

    #[Route('/users-change-password', name: 'users_password')]
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
        return $this->render('security/list_users_password.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/change-password/{id}', name: 'app_change_user_password')]
    public function changeUserPassword(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository $userRepository,
        User $user
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $isAdmin = $user !== $this->getUser();
        $form = $this->createForm(ChangeUserPasswordType::class, $user, [
            'admin' => $isAdmin
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->hashPassword($user, $form->get('newPassword')->getData())
            );
            $userRepository->save();
            $this->addFlash('success', 'Contraseña cambiada correctamente');
            return $this->redirectToRoute('main');
        }
        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
            'admin' => $isAdmin,
        ]);
    }
}