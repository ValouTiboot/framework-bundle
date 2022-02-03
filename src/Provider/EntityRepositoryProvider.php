<?php

namespace Digitix\FrameworkBundle\Provider;

use Digitix\FrameworkBundle\Repository\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class EntityRepositoryProvider
{
	private $registry;

	public function __construct(ManagerRegistry $registry)
	{
		$this->registry = $registry;
	}

	public function getRepository(string $fqnc): EntityRepository
	{
		return new EntityRepository($this->registry, null, $fqnc);
	}
}
