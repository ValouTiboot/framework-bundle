<?php

namespace Digitix\FrameworkBundle\Field;

use Digitix\FrameworkBundle\Field\FieldInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

final class TranslableCollectionField implements FieldInterface
{
	use FieldTrait;

	public static function getInstance($name, $field)
	{
		$currentField = new self();
		$currentField->setLabel($field['label'])
			->setName($name)
			->setFormType(CollectionType::class)
			->addOption('label_format', $field['label'])
			->addOption('entry_type', $field['collectionType'])
			// ->addOption('translation_domain', 'Admin.Fields.Label')
		;

		// if (isset($field['choice']))
		// {
		// 	if (!is_array($field['choice']))
		// 		throw new InvalidArgumentException(sprintf('Invalid choice given for "%s" field. %s given instead of array', $name, gettype($field['choice'])));

		// 	$currentField->addOption('entry_choices', $field['choice']);
		// }
		// else if (isset($field['callback']))
		// 	$currentField->addOption('entry_choices', $currentField->setCallableChoice($field['callback']));

		if (isset($field['disabled'])) {
			$currentField->addOption('disabled', $field['disabled']);
		}

		if (isset($field['required'])) {
			$currentField->addOption('required', $field['required']);
		}

		if (isset($field['help'])) {
			$currentField->addOption('help', $field['help']);
		}

		if (isset($field['attr'])) {
			$currentField->addOption('entry_options', [
				'attr' => $field['attr']
			]);
		}

		return $currentField;
	}

	public function setCallableChoice($callable)
	{
		return $callable();
	}
}
