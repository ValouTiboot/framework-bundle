<?php

namespace Digitix\FrameworkBundle\Field;

use Digitix\FrameworkBundle\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class SubmitField implements FieldInterface
{
	use FieldTrait;

	public static function getInstance($name = 'save', $field = ['label' => 'form.default.submit'])
	{
		$currentField = new self();
		$currentField
			->setName($name)
			->setFormType(SubmitType::class)
			->addOption('label_format', $field['label'])
			->addOption('translation_domain', 'Admin.Form.Default')
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
