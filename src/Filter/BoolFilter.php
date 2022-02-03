<?php

namespace Digitix\FrameworkBundle\Filter;

use Digitix\FrameworkBundle\Filter\FilterInterface;
use Digitix\FrameworkBundle\Search\SearchQueryOperator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class BoolFilter implements FilterInterface
{
	use FilterTrait;

	public static function getInstance($name, $filter, $context)
	{
		return (new self())
			->setLabel($filter['label'])
			->setName($name)
			->setFormType(ChoiceType::class)
			->setOperator(SearchQueryOperator::EQUAL)
			->setAlias(isset($filter['alias']) ? $filter['alias'] : 'a')
			->addOption('placeholder', '')
			->addOption('translation_domain', 'Admin.Fields.Label')
			->addOption('choice_translation_domain', 'Admin.Fields.Label')
			->setChoices()
		;
	}

	public function setChoices()
	{
		$this->addOption('choices', ['yes' => true, 'no' => false]);
		$this->addOption('choice_label', function($choice, $key, $value) {
			return 'label.default.'.$key;
		});
		return $this;
	}
}
