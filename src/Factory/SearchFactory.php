<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Search\Search;
use Digitix\FrameworkBundle\Factory\FormFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Digitix\FrameworkBundle\Factory\FilterFactory;

class SearchFactory
{
	private $filterFactory;
	private $formFactory;

	public function __construct(FilterFactory $filterFactory, FormFactory $formFactory)
	{
		$this->filterFactory = $filterFactory;
		$this->formFactory = $formFactory;
	}

	public function build()
	{
		$clauses = [];

		$filters = $this->filterFactory->build();
		$filtersForm = $this->formFactory->buildFormFilters();

		if ($filtersForm->isSubmitted()) {
			foreach ($filtersForm as $filterForm) {
				if (!$filterForm->isValid()) {
					continue;
				}

				$name = $filterForm->getName();
				$filter = $filters->get($name);
				$value = $filterForm->getData();

				if (is_null($filter) || is_null($value)) {
					continue;
				}

				$search = new Search();
				$search->prepareWhereClause($filter, $filterForm);
				$clauses[$name] = $search;
			}
		}

        return new ArrayCollection($clauses);
	}
}
