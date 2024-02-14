<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddAdminSalleType;
use App\Form\EditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/users/role/client', name: 'user_list_role_client', methods: ['GET'])]
    public function listRoleClient(UserRepository $userRepository): Response
    {
        $users = $userRepository->findByRole('ROLE_CLIENT');

        return $this->render('user/ClientSalle.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/role/AdminSalle', name: 'user_list_role_AdminSalle', methods: ['GET'])]
    public function listRoleAdminSalle(UserRepository $userRepository): Response
    {
        $users = $userRepository->findByRole('ROLE_ADMIN');

        return $this->render('user/AdminSalle.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(['ROLE_CLIENT']);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_list_role_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/new/admin/salle', name: 'app_admin_salle_new', methods: ['GET', 'POST'])]
    public function newAdminSalle(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(AddAdminSalleType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_list_role_AdminSalle', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/newAdminSalle.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function showClient(User $user): Response
    {
        return $this->render('user/showClient.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show_Admin_Salle', methods: ['GET'])]
    public function showAdminSalle(User $user): Response
    {
        return $this->render('user/showAdminSalle.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/editClient', name: 'app_user_edit_Client', methods: ['GET', 'POST'])]
    public function editClient(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_list_role_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editAdmin', name: 'app_user_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_list_role_AdminSalle', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/client/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function deleteClient(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_list_role_client', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/{id}/delete', name: 'app_user_delete_AdminSalle', methods: ['POST'])]
    public function deleteAdminSalle(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_list_role_AdminSalle', [], Response::HTTP_SEE_OTHER);
    }
}
