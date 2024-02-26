<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $form->get('roles')->getData();
            if ($roles !== null) {
                $user->setRoles([$roles]);
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
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
            // do anything else you need here, like send an email

            // return $userAuthenticator->authenticateUser(
            //     $user,
            //     $authenticator,
            //     $request
            // );
            return $this->redirectToRoute('app_login');
        }

        $error = $form->isSubmitted() && !$form->isValid() ? 'Please correct the errors below.' : null;

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        // Extract the user's ID from the signed URL
        $id = $request->query->get('id');
        if (!$id) {
            throw new \InvalidArgumentException('No ID provided');
        }

        // Find the user by ID
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            $this->addFlash('verify_email_error', 'The verification link is invalid or expired.');
            return $this->redirectToRoute('app_register');
        }

        // Mark the user as verified
        $user->setIsVerified(true);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_login');
    }



    #[Route('/simple-test-email')]
    public function simpleTestEmail(MailerInterface $mailer): Response
    {
        // $transport = Transport::fromDsn('smtp://9c24a4ca8dbd5a:0d4e8f851f0380@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login');
        // $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('your-email@example.com')
            ->to('your-recipient@example.com')
            ->subject('Simple Test Email')
            ->text('This is a simple test email.');

        $mailer->send($email);
        dump($mailer);

        return new Response('Simple email sent successfully');
    }

    // #[Route('/test/env')]
    // public function testEnv(): Response
    // {
    //     $dsn = $_ENV['MAILER_DSN'] ?? 'Not found';
    //     return new Response($dsn);
    // }
}
