<?php

namespace Digitix\FrameworkBundle\Provider;

use Digitix\FrameworkBundle\Entity\Configuration;
use Digitix\FrameworkBundle\Provider\EntityRepositoryProvider;

final class ConfigurationProvider
{
	private $entityRepositoryProvider;

	public function __construct(EntityRepositoryProvider $entityRepositoryProvider)
	{
		$this->entityRepositoryProvider = $entityRepositoryProvider;
	}

	public function get(string $name): ?Configuration
	{
		dump($this->entityRepositoryProvider->getRepository('Digitix\\FrameworkBundle\\Entity\\Configuration')->findOneBy(['name' => $name]));
		return $this->entityRepositoryProvider->getRepository('Digitix\\FrameworkBundle\\Entity\\Configuration')->findOneBy(['name' => $name]);
	}

	public function getAll(): ?array
	{
		return $this->entityRepositoryProvider->getRepository('Digitix\\FrameworkBundle\\Entity\\Configuration')->findAll();
	}
}