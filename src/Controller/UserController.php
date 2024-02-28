<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddAdminSalleType;
use App\Form\EditType;
use App\Form\PasswordUpdateType;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;


#[Route('/user')]
class UserController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/users/role/client', name: 'user_list_role_client', methods: ['GET'])]
    public function listRoleClient(UserRepository $userRepository): Response
    {
        // Utilisation de la méthode mise à jour pour inclure le tri par date de création
        $users = $userRepository->findByRoleSortedByCreationDate('ROLE_CLIENT', 'ASC');

        return $this->render('user/ClientSalle.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/role/AdminSalle', name: 'user_list_role_AdminSalle', methods: ['GET'])]
    public function listRoleAdminSalle(UserRepository $userRepository): Response
    {
        // Utilisation de la méthode mise à jour pour inclure le tri par date de création
        $users = $userRepository->findByRoleSortedByCreationDate('ROLE_ADMIN', 'ASC');

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
            $imageFile = $form->get('avatar')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
            $user->setAvatar($newFilename);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('smichimajed@gmail.com', '6Core'))
                    ->to($user->getEmail())
                    ->subject($user->getEmail())
                    ->htmlTemplate('registration/confirmation_email.html.twig'),
                ['id' => $user->getId()]
            );

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
            $imageFile = $form->get('avatar')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
            $user->setAvatar($newFilename);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('smichimajed@gmail.com', '6Core'))
                    ->to($user->getEmail())
                    ->subject($user->getEmail())
                    ->htmlTemplate('registration/confirmation_email.html.twig'),
                ['id' => $user->getId()]
            );


            return $this->redirectToRoute('user_list_role_AdminSalle', [], Response::HTTP_SEE_OTHER);
        }



        return $this->renderForm('user/newAdminSalle.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/my-profile', name: 'app_my_profile', methods: ['GET'])]
    public function myProfile(): Response
    {
        $user = $this->getUser();
        //dump($user);
        return $this->render('user/profileData.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit-profile', name: 'app_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_my_profile');
        }
        return $this->render('user/editProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/Userprofile', name: 'userProfile', methods: ['GET'])]
    public function userProfile(): Response
    {
        $user = $this->getUser();
        //dump($user);
        return $this->render('user/userData.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/editUserProfile', name: 'editProfileUser')]
    public function editProfileUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('userProfile');
        }
        return $this->render('user/editUserProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/showClient/{id}', name: 'app_user_show', methods: ['GET'])]
    public function showClient(User $user): Response
    {
        return $this->render('user/showClient.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/showAdminSalle/{id}', name: 'app_user_show_Admin_Salle', methods: ['GET'])]
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

    #[Route('/{id}/update-password', name: 'app_user_update_password', methods: ['GET', 'POST'])]
    public function updatePassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasswordUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            $this->addFlash('success', 'Password updated successfully.');

            // Determine redirection based on user roles
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('user_list_role_AdminSalle');
            } else {
                return $this->redirectToRoute('user_list_role_client');
            }
        }
        $isClientRole = in_array('ROLE_CLIENT', $user->getRoles());
        return $this->render('user/update_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'isClientRole' => $isClientRole,
        ]);
    }

    // #[Route('/update-passwordProfile', name: 'app_user_update_passwordProfile', methods: ['GET', 'POST'])]
    // public function updatePasswordProfile(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    // {
    //     $user = new User();
    //     $user = $this->getUser();
    //     $form = $this->createForm(PasswordUpdateType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $newPassword = $form->get('newPassword')->getData();
    //         $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
    //         $user->setPassword($hashedPassword);
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         $this->addFlash('success', 'Password updated successfully.');
    //         return $this->redirectToRoute('app_edit_profile');
    //     }

    //     return $this->render('user/update_password.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }



    #[Route('/{id}/editAdmin', name: 'app_user_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_list_role_AdminSalle', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editAdmin.html.twig', [
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

    #[Route('/remove/{id}', name: 'app_user_delete2', methods: ['GET', 'POST'])]
    public function delete2($id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {

        $rec = $userRepository->find($id);
        $entityManager->remove($rec);
        $entityManager->flush();


        return $this->redirectToRoute('user_list_role_AdminSalle', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove1/{id}', name: 'app_user_delete3', methods: ['GET', 'POST'])]
    public function delete3($id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {

        $rec = $userRepository->find($id);
        $entityManager->remove($rec);
        $entityManager->flush();


        return $this->redirectToRoute('user_list_role_client', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-verification', name: 'app_user_toggle_verification', methods: ['POST'])]
    public function toggleVerification(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('toggle-verification' . $user->getId(), $request->request->get('_token'))) {
            // Toggle the verification status
            $user->setIsVerified(!$user->isVerified());

            // Save changes
            $entityManager->persist($user);
            $entityManager->flush();

            // Add a flash message to notify the user of the change
            $this->addFlash(
                'success',
                sprintf('User "%s" verification status has been %s.', $user->getEmail(), $user->isVerified() ? 'activated' : 'deactivated')
            );
        } else {
            // Add a flash message for CSRF token failure
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        // Redirect back to the previous page, or a default page if referrer not available
        $referrer = $request->headers->get('referer');
        return $this->redirect($referrer ?? $this->generateUrl('user_list_role_AdminSalle'));
    }

    #[Route('/{id}/toggle-verification1', name: 'app_user_toggle_verification1', methods: ['POST'])]
    public function toggleVerification1(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('toggle-verification' . $user->getId(), $request->request->get('_token'))) {
            // Toggle the verification status
            $user->setIsVerified(!$user->isVerified());

            // Save changes
            $entityManager->persist($user);
            $entityManager->flush();

            // Add a flash message to notify the user of the change
            $this->addFlash(
                'success',
                sprintf('User "%s" verification status has been %s.', $user->getEmail(), $user->isVerified() ? 'activated' : 'deactivated')
            );
        } else {
            // Add a flash message for CSRF token failure
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        // Redirect back to the previous page, or a default page if referrer not available
        $referrer = $request->headers->get('referer');
        return $this->redirect($referrer ?? $this->generateUrl('user_list_role_client'));
    }



    #[Route('/search', name: 'user_search')]
    public function search(Request $request, UserRepository $userRepository)
    {
        $searchTerm = $request->query->get('q');

        $users = $userRepository->findUsersByStringAndRoleAdmin($searchTerm);

        // Formatage des résultats pour le renvoi au format JSON
        $formattedusers = [];
        foreach ($users as $user) {
            $formattedusers[] = [
                'avatar' => $user->getAvatar(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'adresse' => $user->getAdresse(),
                'num_tele' => $user->getNumTele(),
                'id' => $user->getId(),
                'deleteUrl' => $this->generateUrl('app_user_delete_AdminSalle', ['id' => $user->getId()]),
                'csrfToken' => $this->container->get('security.csrf.token_manager')->getToken('delete' . $user->getId())->getValue(),
                'is_verified' => $user->isVerified(),
            ];
        }

        return new JsonResponse(['users' => $formattedusers]);
    }

    #[Route('/search1', name: 'client_search1')]
    public function search1(Request $request, UserRepository $userRepository)
    {
        $searchTerm = $request->query->get('q');

        $users = $userRepository->findUsersByStringAndRoleClient($searchTerm);

        // Formatage des résultats pour le renvoi au format JSON
        $formattedusers = [];
        foreach ($users as $user) {
            $formattedusers[] = [
                'avatar' => $user->getAvatar(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'adresse' => $user->getAdresse(),
                'num_tele' => $user->getNumTele(),
                'id' => $user->getId(),
            ];
        }

        return new JsonResponse(['users' => $formattedusers]);
    }
}
