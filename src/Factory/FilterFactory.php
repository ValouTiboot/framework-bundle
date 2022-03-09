<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Filter\BoolFilter;
use Digitix\FrameworkBundle\Filter\TextFilter;
use Digitix\FrameworkBundle\Filter\ChoiceFilter;
use Digitix\FrameworkBundle\Filter\EntityFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Config\EntityConfigInterface;

final class FilterFactory
{
	private $context;
	private $filters;
	private static $filterTypes = [
		'bool' => BoolFilter::class,
		'choice' => ChoiceFilter::class,
		'date' => DateFilter::class,
		'entity' => EntityFilter::class,
		'email' => TextFilter::class,
		'text' => TextFilter::class,
	];

	public function __construct(EntityConfigInterface $entityConfig, ContextProvider $contextProvider)
	{
		$this->context = $contextProvider->getContext();
		$this->filters = $entityConfig->getFiltersFields();
	}

	public function build()
	{
		$buildFilters = [];

		if ($this->filters) {
			foreach ($this->filters as $key => $filter) {
				$filterType = $this->findFilterType($filter['type']);
				$buildFilters[$key] = $filterType::getInstance($key, $filter, $this->context);
			}
		}

		return new ArrayCollection($buildFilters);
	}

	private function findFilterType(string $type): string
	{
		return self::$filterTypes[$type] ?? TextFilter::class;
	}
}
