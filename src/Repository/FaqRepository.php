<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Entity\Faq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faq>
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    /**
     * Active questions in the admin order, optionally the first ones only.
     *
     * @return list<Faq>
     */
    public function findActive(?int $limit = null): array
    {
        return $this->findBy(['active' => true], ['position' => 'ASC', 'id' => 'ASC'], $limit);
    }
}
