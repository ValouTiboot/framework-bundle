<?php

namespace Digitix\FrameworkBundle\Field;

use Digitix\FrameworkBundle\Field\FieldInterface;
use Digitix\FrameworkBundle\Field\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

final class PasswordField implements FieldInterface
{
	use FieldTrait;

	public static function getInstance($name, $field)
	{
		$currentField = new self();
		$currentField->setLabel($field['label'])
			->setName($name)
			->setFormType(RepeatedType::class)
			->addOption('type', PasswordType::class)
			->addOption('first_name', $name)
			->addOption('second_name', 'repeat_'.$name)
			->addOption('first_options', ['label' => $field['label']])
			->addOption('second_options', ['label' => $field['label'].'Repeat'])
			->addOption('label_format', $field['label'])
			// ->addOption('translation_domain', 'Admin.Fields.Label')
		;

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
