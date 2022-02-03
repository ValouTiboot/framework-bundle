<?php

namespace Digitix\FrameworkBundle\Field;

use Digitix\FrameworkBundle\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

final class ButtonField implements FieldInterface
{
	use FieldTrait;

	public static function getInstance($name = 'save', $field = [])
	{
		$currentField = new self();
		$currentField
			->setName($name)
			->setFormType(ButtonType::class)
			->addOption('label_format', $field['label'])
			// ->addOption('translation_domain', 'Admin.Fields.Label')
		;

		if (isset($field['disabled'])) {
			$currentField->addOption('disabled', $field['disabled']);
		}

		if (isset($field['attr']) && count($field['attr'])) {
			$currentField->setAttributes($field['attr']);
		}

		return $currentField;
	}
}
