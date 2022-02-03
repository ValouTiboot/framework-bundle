<?php

namespace Digitix\FrameworkBundle\Field;

use Digitix\FrameworkBundle\Field\FieldInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ChoiceField implements FieldInterface
{
	use FieldTrait;

	public static function getInstance($name, $field)
	{
		$currentField = new self();
		$currentField->setLabel($field['label'])
			->setName($name)
			->setFormType(ChoiceType::class)
			->addOption('label_format', $field['label'])
			// ->addOption('translation_domain', 'Admin.Fields.Label')
			// ->addOption('choice_translation_domain', 'Admin.Fields.Label')
		;

		if (isset($field['callback'])) {
			$currentField->addOption('choices', $currentField->setCallableChoice($field['callback']));
		} elseif (isset($field['choice'])) {
			if (!is_array($field['choice'])) {
				throw new InvalidArgumentException(sprintf('Invalid choice given for "%s" field. %s given instead of array', $name, gettype($field['choice'])));
			}

			$currentField->addOption('choices', $field['choice']);
		}

		if (isset($field['expanded'])) {
			$currentField->addOption('expanded', $field['expanded']);
		}

		if (isset($field['multiple'])) {
			$currentField->addOption('multiple', $field['multiple']);
		}

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

	public function setCallableChoice($callable)
	{
		return $callable();
	}
}
