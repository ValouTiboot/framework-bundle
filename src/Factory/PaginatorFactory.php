<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Orm\Paginator;
use Doctrine\Orm\QueryBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaginatorFactory
{
	private $context;
	private $urlGenerator;

	public function __construct(ContextProvider $context, UrlGeneratorInterface $urlGenerator)
	{
		$this->context = $context->getContext();
		$this->urlGenerator = $urlGenerator;
	}

	public function build(QueryBuilder $queryBuilder)
	{
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
