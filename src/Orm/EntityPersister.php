<?php

namespace Digitix\FrameworkBundle\Orm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EntityPersister
{
	private $manager;
	private $validator;

	public function __construct(ManagerRegistry $manager, ValidatorInterface $validator)
	{
		$this->manager = $manager->getManager();
		$this->validator = $validator;
	}

	public function validateObject($persistenObject)
	{
		return $this->validator->validate($persistenObject);
	}

	public function persistObject($persistenObject, $autoDate = true)
	{
		if ($autoDate)
		{
			$dateTime = new \DateTime('now',  new \DateTimeZone('UTC')); // trouver le local plutot que le UTC

			if ($persistenObject->getId() === null)
			{
				if (method_exists($persistenObject, 'setDateAdd'))
					$persistenObject->setDateAdd($dateTime);
			}

			if (method_exists($persistenObject, 'setDateUpd'))
				$persistenObject->setDateupd($dateTime);
		}

		$this->manager->persist($persistenObject);
		$this->manager->flush();

		return true;
	}

	public function persistObjects(ArrayCollection $persistenObjects, $autoDate = true)
	{
		foreach ($persistenObjects as $persistenObject)
		{
			if ($autoDate)
			{
				$dateTime = new \DateTime('now',  new \DateTimeZone('UTC')); // trouver le local plutot que le UTC

				if ($persistenObject->getId() === null)
				{
					if (method_exists($persistenObject, 'setDateAdd'))
						$persistenObject->setDateAdd($dateTime);
				}

				if (method_exists($persistenObject, 'setDateUpd'))
					$persistenObject->setDateupd($dateTime);
			}

			$this->manager->persist($persistenObject);
		}

		$this->manager->flush();

		return true;
	}
}
