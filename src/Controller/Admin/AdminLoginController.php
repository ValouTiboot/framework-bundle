<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Routes are declared by AdminRouteLoader (dgtx_admin_login / dgtx_admin_logout).
 */
final class AdminLoginController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('dgtx_admin_entity_view', ['entityName' => 'dashboard']);
        }

        return $this->render('@DigitixFramework/admin/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    public function logout(): never
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
