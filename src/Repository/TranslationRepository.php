<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Repository;

use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Translation\TranslationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Translation>
 */
class TranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    public static function index(string $domain, string $key): string
    {
        return $domain."\0".Translation::hashKey($key);
    }

    /**
     * Every entry of a locale, indexed by self::index().
     *
     * @return array<string, Translation>
     */
    public function findIndexedByLocale(string $locale): array
    {
        $indexed = [];

        foreach ($this->findBy(['locale' => $locale]) as $translation) {
            $indexed[$translation->getDomain()."\0".$translation->getKeyHash()] = $translation;
        }

        return $indexed;
    }

    /**
     * Translated values, ready to be dumped: locale => domain => key => value.
     * Obsolete keys are included: a key missing from one extraction may well
     * be back at the next one.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public function findValuesGroupedByLocale(?string $locale = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.locale', 't.domain', 't.key', 't.value')
            ->where('t.value IS NOT NULL')
            ->orderBy('t.locale', 'ASC')
            ->addOrderBy('t.domain', 'ASC')
            ->addOrderBy('t.key', 'ASC');

        if (null !== $locale) {
            $qb->andWhere('t.locale = :locale')->setParameter('locale', $locale);
        }

        $grouped = [];

        /** @var array{locale: string, domain: string, key: string, value: string} $row */
        foreach ($qb->getQuery()->getArrayResult() as $row) {
            $grouped[$row['locale']][$row['domain']][$row['key']] = $row['value'];
        }

        return $grouped;
    }

    /**
     * Query of the editor list: one locale, optional domain, status and
     * full-text filters, sorted by domain then key.
     */
    public function createListQueryBuilder(string $locale, ?string $domain = null, ?TranslationStatus $status = null, ?string $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('t.domain', 'ASC')
            ->addOrderBy('t.key', 'ASC');

        if (null !== $domain && '' !== $domain) {
            $qb->andWhere('t.domain = :domain')->setParameter('domain', $domain);
        }

        if (null !== $status) {
            $qb->andWhere('t.status = :status')->setParameter('status', $status->value);
        }

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere('LOWER(t.key) LIKE :search OR LOWER(t.value) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower(trim($search)).'%');
        }

        return $qb;
    }

    /** @return string[] */
    public function findLocales(): array
    {
        return array_column($this->createQueryBuilder('t')
            ->select('DISTINCT t.locale AS locale')
            ->orderBy('t.locale', 'ASC')
            ->getQuery()
            ->getArrayResult(), 'locale');
    }

    /** @return string[] */
    public function findDomains(): array
    {
        return array_column($this->createQueryBuilder('t')
            ->select('DISTINCT t.domain AS domain')
            ->orderBy('t.domain', 'ASC')
            ->getQuery()
            ->getArrayResult(), 'domain');
    }

    /**
     * @return array<string, array<string, int>> locale => status => count
     */
    public function countByLocaleAndStatus(): array
    {
        $counts = [];

        /** @var array{locale: string, status: TranslationStatus|string, total: int|string} $row */
        foreach ($this->createQueryBuilder('t')
            ->select('t.locale', 't.status', 'COUNT(t.id) AS total')
            ->groupBy('t.locale', 't.status')
            ->getQuery()
            ->getArrayResult() as $row) {
            $status = $row['status'] instanceof TranslationStatus ? $row['status']->value : (string) $row['status'];
            $counts[$row['locale']][$status] = (int) $row['total'];
        }

        return $counts;
    }
}
