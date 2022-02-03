<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Search\Search;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormInterface;

class SearchFactory
{
	public function buildSearch(ArrayCollection $filters, FormInterface $filtersForm)
	{
		$clauses = [];

		if ($filtersForm->isSubmitted())
        foreach ($filtersForm as $filterForm)
        {
        	if (!$filterForm->isValid())
        		continue;

        	$name = $filterForm->getName();
        	$filter = $filters->get($name);
        	$value = $filterForm->getData();

        	if (is_null($filter) || is_null($value))
        		continue;

        	$search = new Search();
        	$search->prepareWhereClause($filter, $filterForm);
        	$clauses[$name] = $search;
        }

        return new ArrayCollection($clauses);
	}
}
