<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Security;

use Digitix\FrameworkBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Admin login form (email + password + CSRF), inactive accounts refused.
 */
final class AdminAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'dgtx_admin_login';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = (string) $request->request->get('email', '');

        return new Passport(
            new UserBadge($email, function (string $userIdentifier): User {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

                if (!$user instanceof User) {
                    throw new CustomUserMessageAuthenticationException('Email could not be found.');
                }

                if (!$user->getActive()) {
                    throw new CustomUserMessageAuthenticationException('Account is not active.');
                }

                return $user;
            }),
            new PasswordCredentials((string) $request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', (string) $request->request->get('_csrf_token', '')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        if (null !== ($targetPath = $this->getTargetPath($request->getSession(), $firewallName))) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('dgtx_admin_entity_view', ['entityName' => 'dashboard']));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}
