<?php

namespace Digitix\FrameworkBundle\Search;

use Digitix\FrameworkBundle\Filter\FilterInterface;
use Digitix\FrameworkBundle\Search\SearchQueryOperator;
use Symfony\Component\Form\FormInterface;

class Search implements SearchInterface
{
	private $nameParameter;
	private $valueParameter;
	private $queryString;

	public function prepareWhereClause(FilterInterface $filter, FormInterface $form)
	{
		switch ($filter->getOperator()) {
			case SearchQueryOperator::EQUAL:
			case SearchQueryOperator::NOT_EQUAL:
			case SearchQueryOperator::INF_THAN:
			case SearchQueryOperator::SUP_THAN:
			case SearchQueryOperator::INF_EQUAL_TO:
			case SearchQueryOperator::SUP_EQUAL_TO:
				$this->queryString = sprintf($filter->getAlias().'.%s %s :%s', $filter->getName(), $filter->getOperator(), $filter->getName());
				$this->nameParameter = $filter->getName();
				$this->valueParameter = $form->getData();
				break;

			case SearchQueryOperator::CONTAINS:
			case SearchQueryOperator::NOT_CONTAINS:
				$this->queryString = sprintf($filter->getAlias().'.%s %s :%s', $filter->getName(), $filter->getOperator(), $filter->getName());
				$this->nameParameter = $filter->getName();
				$this->valueParameter = '%'.$form->getData().'%';
				break;
		}
	}

	public function getNameParameter()
	{
		return $this->nameParameter;
	}

	public function getValueParameter()
	{
		return $this->valueParameter;
	}

	public function getQueryString()
	{
		return $this->queryString;
	}

}
