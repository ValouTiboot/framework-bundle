<?php

namespace Digitix\FrameworkBundle\Orm;

use Digitix\FrameworkBundle\Context\Context;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Paginator
{
	private $queryBuilder;
	private $itemPerPage = 30;
	private $currentPage = 1;
	private $total = 0;
	private $results;
	private $totalPages = 1;
	private $context;
	private $urlGenerator;

	public function paginate()
	{
		$offset = ($this->currentPage-1) * $this->itemPerPage;
		$query = $this->queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($this->itemPerPage)
            ->getQuery()
		;

        if (0 === \count($this->queryBuilder->getDQLPart('join'))) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $paginator = new OrmPaginator($query, true);

        // if (null === $useOutputWalkers = $paginatorDto->useOutputWalkers()) {
        //     $havingPart = $queryBuilder->getDQLPart('having');
        //     if (\is_array($havingPart)) {
        //         $useOutputWalkers = \count($havingPart) > 0;
        //     } else {
        //         $useOutputWalkers = null !== $havingPart;
        //     }
        // }
        // $paginator->setUseOutputWalkers($useOutputWalkers);

        $this->results = $paginator->getIterator();
        $this->total = $paginator->count();
        $this->totalPages = (int)ceil($this->total / $this->itemPerPage);

        return $this;
	}

	public function hasPrevPage(): bool
	{
		return $this->currentPage > 1;
	}

	public function hasNPage(): bool
	{
		return $this->currentPage < $this->getLastPage();
	}

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

	public function getLastPage(): int
	{
		return ceil($this->total / $this->itemPerPage);
	}

	public function getFirstPage(): int
	{
		return 1;
	}

	public function getTotalPages(): int
	{
		return $this->totalPages;
	}

	public function getPageLink($pageNumber)
	{
		return $this->generatePageLink($pageNumber);
	}

	public function generatePageLink($pageNumber, array $routeParams = [])
	{
		$route = $this->context->getRequest()->attributes->get('_route');
		$params = $this->context->getRequest()->query->all();

		if (!count($routeParams)) {
			if ($this->context->getEntityName() != '') {
				$routeParams = [
					'entityName' => lcfirst($this->context->getEntityName())
				];
			}
		}

		$routeQuery = array_merge($params, $routeParams, ['page' => $pageNumber]);

		return $this->urlGenerator->generate($route, $routeQuery, UrlGeneratorInterface::ABSOLUTE_URL);
	}

	public function setQueryBuilder(QueryBuilder $queryBuilder): self
	{
		$this->queryBuilder = $queryBuilder;
		return $this;
	}

	public function setItemPerPage(int $itemPerPage): self
	{
		$this->itemPerPage = $itemPerPage;
		return $this;
	}

	public function getItemPerPage(): int
	{
		return $this->itemPerPage;
	}

	public function setCurrentPage(int $currentPage): self
	{
		$this->currentPage = $currentPage;
		return $this;
	}

	public function getCurrentPage(): int
	{
		return $this->currentPage;
	}

	public function getResults(): ?iterable
	{
		return $this->results;
	}

	public function getTotal(): int
	{
		return $this->total;
	}

	public function setContext(Context $context): self
	{
		$this->context = $context;
		return $this;
	}

	public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): self
	{
		$this->urlGenerator = $urlGenerator;
		return $this;
	}
}
