<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\List;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Page of results for a list, with the helpers the pagination template needs.
 */
final class Paginator
{
    /** @var object[] */
    private array $results = [];
    private int $total = 0;
    private bool $paginated = false;

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly int $currentPage,
        private readonly int $itemsPerPage,
        private readonly Request $request,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly string $entitySlug,
    ) {
    }

    public static function pageFromRequest(Request $request): int
    {
        return max(1, $request->query->getInt('page', 1));
    }

    public function paginate(): self
    {
        if ($this->paginated) {
            return $this;
        }

        $query = $this->queryBuilder
            ->setFirstResult(($this->currentPage - 1) * $this->itemsPerPage)
            ->setMaxResults($this->itemsPerPage)
            ->getQuery();

        $hasJoin = \count($this->queryBuilder->getDQLPart('join')) > 0;

        if (!$hasJoin) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $ormPaginator = new OrmPaginator($query, $hasJoin);

        $this->results = iterator_to_array($ormPaginator->getIterator(), false);
        $this->total = \count($ormPaginator);
        $this->paginated = true;

        return $this;
    }

    /** @return object[] */
    public function getResults(): array
    {
        return $this->paginate()->results;
    }

    public function getTotal(): int
    {
        return $this->paginate()->total;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTotalPages(): int
    {
        return max(1, (int) ceil($this->getTotal() / $this->itemsPerPage));
    }

    public function getFirstPage(): int
    {
        return 1;
    }

    public function getLastPage(): int
    {
        return $this->getTotalPages();
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getTotalPages();
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function getNextPage(): int
    {
        return min($this->getTotalPages(), $this->currentPage + 1);
    }

    public function getPageLink(int $page): string
    {
        $params = array_merge($this->request->query->all(), [
            'entityName' => $this->entitySlug,
            'page' => $page,
        ]);

        return $this->urlGenerator->generate((string) $this->request->attributes->get('_route'), $params);
    }
}
