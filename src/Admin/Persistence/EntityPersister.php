<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Persistence;

use Doctrine\Persistence\ManagerRegistry;

/**
 * Thin persistence helper: persists/flushes and maintains the conventional
 * dateAdd / dateUpd timestamps when the entity exposes their setters.
 */
final class EntityPersister
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function save(object $entity, bool $touchTimestamps = true): void
    {
        $manager = $this->managerFor($entity);

        if ($touchTimestamps) {
            $this->touch($entity, $manager->contains($entity));
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
        $now = new \DateTimeImmutable();

        if (!$isManaged && method_exists($entity, 'setDateAdd') && (!method_exists($entity, 'getDateAdd') || null === $entity->getDateAdd())) {
            $entity->setDateAdd($now);
        }

        if (method_exists($entity, 'setDateUpd')) {
            $entity->setDateUpd($now);
        }
    }

    private function managerFor(object $entity): \Doctrine\Persistence\ObjectManager
    {
        return $this->registry->getManagerForClass($entity::class)
            ?? throw new \LogicException(sprintf('"%s" is not a managed Doctrine entity.', $entity::class));
    }
}
