<?php


namespace Digitix\FrameworkBundle\Field;

use Digitix\FrameworkBundle\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class BoolField implements FieldInterface
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
			->addOption('expanded', true)
			->addOption('multiple', false)
			->addAttribute('class', 'dgtx-switch')
			->setChoices()
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

	public function setChoices()
	{
		$this->addOption('choices', ['yes' => true, 'no' => false]);
		$this->addOption('choice_label', function($choice, $key, $value) {
			return 'label.default.'.$key;
		});
		return $this;
	}
}
