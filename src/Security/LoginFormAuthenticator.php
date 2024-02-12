<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    private $userRepository;
    private $urlGenerator;

    public function __construct(UserRepository $userRepository, UrlGeneratorInterface $urlGenerator)
    {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    // public function getCredentials(Request $request)
    // {
    //     $credentials=[
    //         'email' =>$request->request->get('email'),
    //         'password' =>$request->request->get('MotDePasse'),
    //         '_csrf_token' =>$request->request->get('_csrf_token'),
    //     ];
    //     $request->getSession()->set(Security::LAST_USERNAME,$credentials['email']);
    //     return $credentials;
    // }

    // public function authenticate(Request $request): Passport
    // {
    //     $email = $request->request->get('email');
    //     $password = $request->request->get('password');
    //     $csrfToken = $request->request->get('_csrf_token');

    //     return new Passport(
    //         new UserBadge($email, function($userIdentifier) {
    //             return $this->userRepository->findOneByEmail($userIdentifier);
    //         }),
    //         new PasswordCredentials($password),
    //         [
    //             new CsrfTokenBadge('authenticate', $csrfToken),
    //         ]
    //     );
    // }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        $userBadge = new UserBadge($email, function ($userIdentifier) {
            $user = $this->userRepository->findOneByEmail($userIdentifier);
            if (!$user) {

                throw new CustomUserMessageAuthenticationException('Email not registered yet.');
            }
            return $user;
        });

        return new Passport(
            $userBadge,
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }


    // public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    // {
    //     if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
    //         return new RedirectResponse($targetPath);
    //     }

    //     // For example, redirect to the homepage
    //     return new RedirectResponse($this->urlGenerator->generate('app_nom'));
    // }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Check for the role and redirect accordingly
        if (in_array('ROLE_ADMIN', $token->getRoleNames())) {
            // Redirect to the admin dashboard if the user has the ROLE_ADMIN role
            return new RedirectResponse($this->urlGenerator->generate('app_salle'));
        } elseif (in_array('ROLE_CLIENT', $token->getRoleNames())) {

            return new RedirectResponse($this->urlGenerator->generate('app_nom'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
