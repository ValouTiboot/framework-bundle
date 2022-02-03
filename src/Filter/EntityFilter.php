<?php

namespace Digitix\FrameworkBundle\Filter;

use Digitix\FrameworkBundle\Filter\FilterInterface;
use Digitix\FrameworkBundle\Search\SearchQueryOperator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class EntityFilter implements FilterInterface
{
	use FilterTrait;

	public static function getInstance($name, $filter, $context)
	{
		return (new self())
			->setLabel($filter['label'])
			->setName($name)
			->setFormType(EntityType::class)
			->setOperator(SearchQueryOperator::EQUAL)
			->setAlias(isset($filter['alias']) ? $filter['alias'] : 'a')
			->addOption('placeholder', '')
			->addOption('class', 'Digitix\FrameworkBundle\\Entity\\'.ucfirst((isset($filter['collection']) && isset($filter['collection']['name']) ? $filter['collection']['name'] : $name)))
			->addOption('choice_label', (isset($filter['collection']) && isset($filter['collection']['label']) ? $filter['collection']['label'] : 'name'))
			->addOption('choice_value', (isset($filter['collection']) && isset($filter['collection']['value']) ? $filter['collection']['value'] : 'id'))
		;
	}
}
