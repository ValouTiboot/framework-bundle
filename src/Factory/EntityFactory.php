<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Entity\Magic;
use Digitix\FrameworkBundle\Presenter\EntityPresenter;
use Digitix\FrameworkBundle\Provider\EntityRepositoryProvider;

final class EntityFactory
{
	private $entityPresenter;
	private $repositoryProvider;

	public function __construct(EntityPresenter $entityPresenter, EntityRepositoryProvider $repositoryProvider)
	{
		$this->entityPresenter = $entityPresenter;
		$this->repositoryProvider = $repositoryProvider;
	}

	public function build($entityName, $entityId = null)
	{
		$this->entityPresenter
			->setName($entityName)
			->setFqcn('Digitix\\FrameworkBundle\\Entity\\'.ucfirst($entityName))
			->setPrimaryKeyValue($entityId)
			->setInstance($this->createEntityInstance())
		;
		return $this->entityPresenter;
	}

	public function createEntityInstance()
	{
		$entityInstance = null;

		if ($this->entityPresenter->getName() !== null && class_exists($this->entityPresenter->getFqcn())) {
			$entityFqcn = $this->entityPresenter->getFqcn();

			if ($this->entityPresenter->getPrimaryKeyValue() !== null) {
				$entityInstance = $this->repositoryProvider
					->getRepository($this->entityPresenter->getFqcn())
					->find($this->entityPresenter->getPrimaryKeyValue())
				;
			} else {
				$entityInstance = new $entityFqcn;
			}
		} else {
			$entityInstance = new Magic();
		}

		if (is_object($entityInstance)
			&& property_exists($entityInstance, 'translations')
			&& $this->entityPresenter->getPrimaryKeyValue() === null
		) {
			$fqcnTranslation = $this->entityPresenter->getFqcn().'Translation';
			$languages = $this->repositoryProvider
				->getRepository('Digitix\\FrameworkBundle\\Entity\\Language')
				->findBy(
					['active' => 1],
					['defaultLanguage' => 'ASC']
				)
			;

			foreach ($languages as $language) {
				$entityTranslation = new $fqcnTranslation();
				$entityTranslation->setLanguage($language);
				$entityInstance->addTranslation($entityTranslation);
			}
		}

		return $entityInstance;
	}
}
