<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Field\BoolField;
use Digitix\FrameworkBundle\Field\DateField;
use Digitix\FrameworkBundle\Field\TextField;
use Digitix\FrameworkBundle\Field\EmailField;
use Digitix\FrameworkBundle\Field\ButtonField;
use Digitix\FrameworkBundle\Field\ChoiceField;
use Digitix\FrameworkBundle\Field\EntityField;
use Digitix\FrameworkBundle\Field\HiddenField;
use Digitix\FrameworkBundle\Field\SearchField;
use Digitix\FrameworkBundle\Field\SubmitField;
use Digitix\FrameworkBundle\Field\PasswordField;
use Digitix\FrameworkBundle\Field\TextareaField;
use Doctrine\Common\Collections\ArrayCollection;
use Digitix\FrameworkBundle\Config\AdminFormConfigInterface;
use Digitix\FrameworkBundle\Field\TranslableCollectionField;

final class FieldFactory
{
	private $fields;
	private $adminFormConfig;
	private static $fieldTypes = [
		'bool' 		=> BoolField::class,
		'button' 	=> ButtonField::class,
		'choice' 	=> ChoiceField::class,
		'date' 		=> DateField::class,
		'email' 	=> EmailField::class,
		'entity' 	=> EntityField::class,
		'hidden' 	=> HiddenField::class,
		'password' 	=> PasswordField::class,
		'search' 	=> SearchField::class,
		'submit' 	=> SubmitField::class,
		'text' 		=> TextField::class,
		'textarea' 	=> TextareaField::class,
		'translate' => TranslableCollectionField::class,
	];

	public function __construct(AdminFormConfigInterface $adminFormConfig)
	{
		$this->adminFormConfig = $adminFormConfig;
	}

	public function build()
	{
		$buildFields = [];
		$this->fields = $this->adminFormConfig->getFormFieldsConfig();
		$translationDomain = $this->adminFormConfig->getTranslationDomain();

		if (count($this->fields)) {
			foreach ($this->fields as $key => $field) {
				$fieldType = $this->findFieldType($field['type']);
				$fieldInstance = $fieldType::getInstance($key, $field)->addOption('translation_domain', $translationDomain);

				if ($field['type'] == 'choice') {
					$fieldInstance->addOption('choice_translation_domain', $translationDomain);
				}

				$buildFields[$key] = $fieldInstance;
			}

			if ($this->adminFormConfig->getHasAutoSubmitButton()) {
				$buildFields['save'] = SubmitField::getInstance();
			}
		}

		return new ArrayCollection($buildFields);
	}

	private function findFieldType(string $type): string
	{
		return self::$fieldTypes[$type] ?? TextField::class;
	}
}
