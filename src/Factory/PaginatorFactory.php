<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Orm\Paginator;
use Digitix\FrameworkBundle\Sorter\Sorter;
use Digitix\FrameworkBundle\Factory\SearchFactory;
use Digitix\FrameworkBundle\Factory\SorterFactory;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Repository\EntityRepository;
use Digitix\FrameworkBundle\Config\AdminListConfigInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaginatorFactory
{
	private $context;
	private $entityRepository;
	private $searchFactory;
	private $urlGenerator;

	public function __construct(
		ContextProvider $context,
		EntityRepository $entityRepository,
		SearchFactory $searchFactory,
		UrlGeneratorInterface $urlGenerator
	)
	{
		$this->context = $context->getContext();
		$this->entityRepository = $entityRepository;
		$this->searchFactory = $searchFactory;
		$this->urlGenerator = $urlGenerator;
	}

	public function build($listFields, Sorter $sorter)
	{
		$queryBuilder = $this->entityRepository->buildQuery(
			$listFields,
			$this->searchFactory->build(),
			$sorter
		);

		/**
		 * TODO Can ->setItemPerPage on paginator
		 */
		$paginator = new Paginator();
		$paginator
			->setQueryBuilder($queryBuilder)
			->setCurrentPage($this->context->getRequest()->query->get('page',1))
			->setContext($this->context)
			->setUrlGenerator($this->urlGenerator)
		;

		return $paginator;
	}
}
