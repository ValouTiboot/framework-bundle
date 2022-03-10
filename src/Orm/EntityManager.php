<?php

namespace Digitix\FrameworkBundle\Orm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EntityManager
{
	private $manager;
	private $validator;

	public function __construct(ManagerRegistry $manager, ValidatorInterface $validator)
	{
		$this->manager = $manager->getManager();
		$this->validator = $validator;
	}

	public function validateObject($entity)
	{
		return $this->validator->validate($entity);
	}

	public function persistEntity($entity, $autoDate = true)
	{
		if ($autoDate) {
			$dateTime = new \DateTime('now',  new \DateTimeZone('UTC')); // trouver le local plutot que le UTC

			if ($entity->getId() === null) {
				if (method_exists($entity, 'setDateAdd')) {
					$entity->setDateAdd($dateTime);
				}
			}

			if (method_exists($entity, 'setDateUpd')) {
				$entity->setDateupd($dateTime);
			}
		}

		$this->manager->persist($entity);
		$this->manager->flush();

		return true;
	}

	public function persistEntities(ArrayCollection $entitys, $autoDate = true)
	{
		foreach ($entitys as $entity) {
			if ($autoDate) {
				$dateTime = new \DateTime('now',  new \DateTimeZone('UTC')); // trouver le local plutot que le UTC

				if ($entity->getId() === null) {
					if (method_exists($entity, 'setDateAdd')) {
						$entity->setDateAdd($dateTime);
					}
				}

				if (method_exists($entity, 'setDateUpd')) {
					$entity->setDateupd($dateTime);
				}
			}

			$this->manager->persist($entity);
		}

		$this->manager->flush();

		return true;
	}

	public function removeEntity($entity)
	{
        /**
        * TODO delete images if have some
        */

        $this->manager->remove($entity);
        $this->manager->flush();

		return true;
	}
}
