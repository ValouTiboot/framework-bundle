<?php

namespace Digitix\FrameworkBundle\Field;

use Digitix\FrameworkBundle\Field\FieldInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class EntityField implements FieldInterface
{
	use FieldTrait;

	public static function getInstance($name, $field)
	{
		$currentField = new self();
		$currentField->setLabel($field['label'])
			->setName($name)
			->setFormType(EntityType::class)
			->addOption('class', 'Digitix\FrameworkBundle\\Entity\\'.ucfirst((isset($field['collection']) && isset($field['collection']['name']) ? $field['collection']['name'] : $name)))
			->addOption('choice_label', (isset($field['collection']) && isset($field['collection']['label']) ? $field['collection']['label'] : 'name'))
			->addOption('choice_value', (isset($field['collection']) && isset($field['collection']['value']) ? $field['collection']['value'] : 'id'))
			->addOption('label_format', $field['label'])
			// ->addOption('translation_domain', 'Admin.Fields.Label')
		;

		if (!isset($field['required']) || (isset($field['required']) && $field['required'] === false )) {
			$currentField->addOption('placeholder', '');
		}

		if (isset($field['disabled'])) {
			$currentField->addOption('disabled', $field['disabled']);
		}

		if (isset($field['required'])) {
			$currentField->addOption('required', $field['required']);
		}

		if (isset($field['help'])) {
			$currentField->addOption('help', $field['help']);
		}

		if (isset($field['attr']) && count($field['attr'])) {
			$currentField->setAttributes($field['attr']);
		}

		return $currentField;
	}
}
