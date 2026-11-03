<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * Thin persistence helper: persists/flushes and maintains the conventional
 * dateAdd / dateUpd timestamps when the entity exposes their setters. A new
 * entity with a "position" field saved alone (admin create form) goes last:
 * MAX(position) + 1; an update never touches it.
 */
final class EntityPersister
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function save(object $entity, bool $touchTimestamps = true): void
    {
        $manager = $this->managerFor($entity);
        $isManaged = $manager->contains($entity);

        if (!$isManaged) {
            $this->placeLast($entity, $manager);
        }

        if ($touchTimestamps) {
            $this->touch($entity, $isManaged);
        }

        $manager->persist($entity);
        $manager->flush();
    }

    /**
     * @param iterable<object> $entities
     */
    public function saveAll(iterable $entities, bool $touchTimestamps = true): void
    {
        $manager = null;

        foreach ($entities as $entity) {
            $manager ??= $this->managerFor($entity);

            if ($touchTimestamps) {
                $this->touch($entity, $manager->contains($entity));
            }

            $manager->persist($entity);
        }

        $manager?->flush();
    }

    public function remove(object $entity): void
    {
        $manager = $this->managerFor($entity);
        $manager->remove($entity);
        $manager->flush();
    }

    private function touch(object $entity, bool $isManaged): void
    {
        // Doctrine "datetime" columns (DBAL 4) only accept mutable DateTime instances.
        $now = new \DateTime();

        if (!$isManaged && method_exists($entity, 'setDateAdd') && (!method_exists($entity, 'getDateAdd') || null === $entity->getDateAdd())) {
            $entity->setDateAdd($now);
        }

        if (method_exists($entity, 'setDateUpd')) {
            $entity->setDateUpd($now);
        }
    }

    private function placeLast(object $entity, ObjectManager $manager): void
    {
        if (!method_exists($entity, 'setPosition') || !$manager instanceof EntityManagerInterface || !$manager->getClassMetadata($entity::class)->hasField('position')) {
            return;
        }

        $max = $manager->createQueryBuilder()
            ->select('MAX(e.position)')
            ->from($entity::class, 'e')
            ->getQuery()
            ->getSingleScalarResult();

        $entity->setPosition((int) $max + 1);
    }

    private function managerFor(object $entity): ObjectManager
    {
        return $this->registry->getManagerForClass($entity::class)
            ?? throw new \LogicException(sprintf('"%s" is not a managed Doctrine entity.', $entity::class));
    }
}
