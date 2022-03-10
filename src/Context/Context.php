<?php

namespace Digitix\FrameworkBundle\Context;

use Twig\Environment;
use Digitix\FrameworkBundle\Entity\Language;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class Context
{
	const CONTEXT_ID = 'dgtx_context';

	private $user;
	private $configuration;
	private $controller;
	private $entityName;
	private $entity;
	private $cookie;
	private $language;
	private $translator;
	private $request;
	public $twig;

	public function __construct(?UserInterface $user, Environment $twig, TranslatorInterface $translator)
	{
		$this->user = $user ?? null;
		$this->twig = $twig;
		$this->translator = $translator;
	}

	public function getUser() : ?UserInterface
	{
		return $this->user;
	}

	public function getTwig() : Environment
	{
		return $this->twig;
	}

	public function setController($controller)
	{
		$this->controller = $controller;
		return $this;
	}

	public function getControler()
	{
		return $this->controller;
	}

	public function setEntity($entity)
	{
		$this->entity = $entity;
		return $this;
	}

	public function getEntity()
	{
		return $this->entity;
	}

	public function setEntityName($entityName): self
	{
		$this->entityName = ucfirst($entityName);
		return $this;
	}

	public function getEntityName()
	{
		return $this->entityName;
	}

	public function getEntityFqcn()
	{
		return 'Digitix\\FrameworkBundle\\Entity\\'.$this->getEntityName();
	}

	public function setRequest(Request $request): self
	{
		$this->request = $request;
		return $this;
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function setTranslator($translator)
	{
		$this->translator = $translator;
		return $this;
	}

	public function getTranslator()
	{
		return $this->translator;
	}

	public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null)
	{
		return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
	}

	public function setLanguage(Language $language): self
	{
		$this->language = $language;
		return $this;
	}

	public function getLanguage(): ?Language
	{
		return $this->language;
	}

	public function setConfiguration(ArrayCollection $configuration): self
	{
		$this->configuration = $configuration;
		return $this;
	}

	public function getConfiguration($key): string|null
	{
		return $this->configuration->get($key);
	}

	public function getConfigurations(): array
	{
		return $this->configuration->toArray();
	}
}
