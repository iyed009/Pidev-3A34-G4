<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Security\GoogleAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use League\OAuth2\Client\Provider\GoogleUser;


class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // Redirige l'utilisateur vers Google pour l'authentification
        return $clientRegistry->getClient('google')->redirect(['profile', 'email'], []);
    }

    // #[Route('/connect/google/check', name: 'connect_google_check')]
    // public function connectCheckAction(Request $request, ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, UserRepository $userRepository, UserAuthenticatorInterface $userAuthenticator, GoogleAuthenticator $authenticator)
    // {
    //     // This assumes you've set up a custom Authenticator `GoogleAuthenticator`
    //     $client = $clientRegistry->getClient('google');
    //     $googleUser = $client->fetchUser();

    //     // Logic to find or create your User entity
    //     $email = $googleUser->getEmail();
    //     $user = $userRepository->findOneBy(['email' => $email]);

    //     if (!$user) {
    //         $user = new User();
    //         // Set user properties
    //         $entityManager->persist($user);
    //         $entityManager->flush();
    //     }

    //     // Perform programmatic login
    //     return $userAuthenticator->authenticateUser(
    //         $user,
    //         $authenticator,
    //         $request
    //     );
    // }
}
