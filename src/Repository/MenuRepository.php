<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Entity\Menu;
use Digitix\FrameworkBundle\Entity\MenuItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    /** A menu by its code ("main") or by its id. */
    public function findOneByCodeOrId(string|int $identifier): ?Menu
    {
        if (\is_int($identifier) || ctype_digit($identifier)) {
            return $this->find((int) $identifier);
        }

        return $this->findOneBy(['code' => strtolower($identifier)]);
    }

    /**
     * Every item of a menu with its translations and parent loaded, parents
     * before children, siblings in order.
     *
     * @return MenuItem[]
     */
    public function findItems(Menu $menu): array
    {
        /** @var MenuItem[] $items */
        $items = $this->getEntityManager()->createQuery(
            'SELECT i, t, l, p FROM '.MenuItem::class.' i'
            .' LEFT JOIN i.translations t LEFT JOIN t.language l LEFT JOIN i.parent p'
            .' WHERE i.menu = :menu ORDER BY i.depth ASC, i.position ASC, i.id ASC'
        )->setParameter('menu', $menu)->getResult();

        return $items;
    }
}
