<?php

namespace Digitix\FrameworkBundle\Filter;

use Digitix\FrameworkBundle\Filter\FilterInterface;
use Digitix\FrameworkBundle\Search\SearchQueryOperator;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class TextFilter implements FilterInterface
{
	use FilterTrait;

	public static function getInstance($name, $filter, $context)
	{
		return (new self())
			->setLabel($filter['label'])
			->setName($name)
			->setFormType(TextType::class)
			->setOperator(SearchQueryOperator::CONTAINS)
			->addAttribute('placeholder', $context->trans($filter['label'], [], 'Admin.Fields.Label'))
			->setAlias(isset($filter['alias']) ? $filter['alias'] : 'a')
		;
	}
}
