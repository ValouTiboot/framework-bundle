<?php

namespace Digitix\FrameworkBundle\Sorter;

use Digitix\FrameworkBundle\Context\Context;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Sorter\SorterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class Sorter implements SorterInterface
{
	private $context;
	private $UrlGenerator;
	private $orderBy = 'id';
	private $orderWay = 'asc';

	public function __construct(ContextProvider $context, UrlGeneratorInterface $urlGenerator)
	{
		$this->context = $context->getContext();
		$this->urlGenerator = $urlGenerator;
	}

	public function setOrderBy(string $orderBy): self
	{
		if (!empty($orderBy))
			$this->orderBy = $orderBy;

		return $this;
	}

	public function getOrderBy(): string
	{
		return $this->orderBy;
	}

	public function setOrderWay(string $orderWay): self
	{
		if (!empty($orderWay))
			$this->orderWay = $orderWay;

		return $this;
	}

	public function getOrderWay(): string
	{
		return $this->orderWay;
	}

	public function generateLink($fieldName, $direction)
	{
		$route = $this->context->getRequest()->attributes->get('_route');
		$params = $this->context->getRequest()->query->all();
		$route_query = array_merge($params, ['entityName' => strtolower($this->context->getEntityName()), 'sortBy' => $fieldName, 'sortWay' => $direction]);

		return $this->urlGenerator->generate($route, $route_query, UrlGeneratorInterface::ABSOLUTE_URL);
	}

	public function getContext(): Context
	{
		return $this->context;
	}
}
