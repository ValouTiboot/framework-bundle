<?php

namespace Digitix\FrameworkBundle\Filter;

use Digitix\FrameworkBundle\Filter\FilterInterface;
use Digitix\FrameworkBundle\Search\SearchQueryOperator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ChoiceFilter implements FilterInterface
{
	use FilterTrait;

	public static function getInstance($name, $filter, $context)
	{
		$currentFilter = new self();
		$currentFilter
			->setLabel($filter['label'])
			->setName($name)
			->setFormType(ChoiceType::class)
			->setOperator(SearchQueryOperator::EQUAL)
			->setAlias(isset($filter['alias']) ? $filter['alias'] : 'a')
			->addOption('placeholder', '')
			->addOption('translation_domain', 'Admin.Fields.Label')
			->addOption('choice_translation_domain', 'Admin.Fields.Label')
		;

		if (isset($filter['choice']))
		{
			if (!is_array($filter['choice']))
				throw new InvalidArgumentException(sprintf('Invalid choice given for "%s" filter. %s given instead of array', $name, gettype($filter['choice'])));

			$currentFilter->addOption('choices', $filter['choice']);
			$currentFilter->addOption('choice_label', function($choice, $key, $value) {
				return $key;
			});
		}
		else if (isset($filter['callback']))
			$currentFilter->addOption('choices', $currentFilter->setCallableChoice($filter['callback']));

		return $currentFilter;
	}

	public function setCallableChoice($callable)
	{
		return $callable();
	}
}