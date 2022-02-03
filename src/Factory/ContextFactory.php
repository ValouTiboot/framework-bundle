<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Context\Context;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ContextFactory
{
	private $tokenStorage;
	private $twig;
	private $translator;

	public function __construct(?TokenStorageInterface $tokenStorage, Environment $twig, TranslatorInterface $translator)
	{
		$this->tokenStorage = $tokenStorage;
		$this->twig = $twig;
		$this->translator = $translator;
	}

	public function createContext() : Context
	{
		return new Context($this->getUser($this->tokenStorage), $this->twig, $this->translator);
	}

	private function getUser(?TokenStorageInterface $tokenStorage): ?UserInterface
    {
        if (null === $tokenStorage || !$token = $tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();

        return \is_object($user) ? $user : null;
    }
}
