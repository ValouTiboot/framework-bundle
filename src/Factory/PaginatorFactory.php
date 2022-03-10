<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Orm\Paginator;
use Digitix\FrameworkBundle\Factory\SearchFactory;
use Digitix\FrameworkBundle\Factory\SorterFactory;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Repository\EntityRepository;
use Digitix\FrameworkBundle\Config\AdminListConfigInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaginatorFactory
{
	private $context;
	private $adminListConfig;
	private $entityRepository;
	private $searchFactory;
	private $sorterFactory;
	private $urlGenerator;

	public function __construct(
		ContextProvider $context,
		AdminListConfigInterface $adminListConfig,
		EntityRepository $entityRepository,
		SearchFactory $searchFactory,
		SorterFactory $sorterFactory,
		UrlGeneratorInterface $urlGenerator
	)
	{
		$this->context = $context->getContext();
		$this->adminListConfig = $adminListConfig;
		$this->entityRepository = $entityRepository;
		$this->searchFactory = $searchFactory;
		$this->sorterFactory = $sorterFactory;
		$this->urlGenerator = $urlGenerator;
	}

	public function build()
	{
		/**
		 * TODO Can ->setItemPerPage on paginator : default 50 Fron adminListConfig yml
		 */
		$paginator = new Paginator();
		$paginator
			->setQueryBuilder($this->buildQuery())
			->setCurrentPage($this->context->getRequest()->query->get('page',1))
			->setContext($this->context)
			->setUrlGenerator($this->urlGenerator)
		;

		return $paginator;
	}

	protected function buildQuery()
	{
		return $this->entityRepository->buildQuery(
			$this->adminListConfig->getListFields(),
			$this->searchFactory->build(),
			$this->sorterFactory->build()
		);
	}
}
