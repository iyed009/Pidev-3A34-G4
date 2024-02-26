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

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        if (empty($email) || empty($password)) {
            throw new CustomUserMessageAuthenticationException('Fields must not be empty.');
        }

        $userBadge = new UserBadge($email, function ($userIdentifier) {
            $user = $this->userRepository->findOneByEmail($userIdentifier);
            if (!$user) {
                throw new CustomUserMessageAuthenticationException('Email not registered yet.');
            }

            // Check if the user is verified. If not, throw an exception.
            if (!$user->isVerified()) {
                throw new CustomUserMessageAuthenticationException('Your account is not verified. Please check your email to verify your account.');
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



    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        if (in_array('ROLE_ADMIN', $token->getRoleNames())) {

            return new RedirectResponse($this->urlGenerator->generate('app_salle_index'));
        }

        if (in_array('ROLE_SUPER_ADMIN', $token->getRoleNames())) {

            return new RedirectResponse($this->urlGenerator->generate('app_nom'));
        } elseif (in_array('ROLE_CLIENT', $token->getRoleNames())) {

            return new RedirectResponse($this->urlGenerator->generate('app_client'));
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
