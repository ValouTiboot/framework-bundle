<?php

namespace Digitix\FrameworkBundle\Config;

use Digitix\FrameworkBundle\Config\FieldConfigInterface;
use Digitix\FrameworkBundle\Provider\ContextProvider;

class FieldConfig implements FieldConfigInterface
{
	private $context;
	private $entityName;
	private $configuration;
	private $_form = [];

	public function __construct(array $params, ContextProvider $context)
	{
		$this->context = $context->getContext();
		$this->entityName = $this->context->getEntityName();
		$this->configuration = $params['admin_entities'][$this->entityName];

		if (isset($this->configuration['form']) && count($this->configuration['form'])) {
			$this->_form = $this->configuration['form'];
		}
	}

	public function getFormFieldsConfig(): array
	{
		return isset($this->_form['fields']) ? $this->_form['fields'] : [];
	}

	public function getHasReturnLink(): bool
	{
		return isset($this->_form['has_return_link']) ? $this->_form['has_return_link'] : true;
	}

	public function getTranslationDomain(): string
	{
		return isset($this->_form['translation_domain']) ? $this->_form['translation_domain'] : 'Admin.Fields.Label';
	}

	public function getHasAutoSubmitButton(): bool
	{
		return isset($this->configuration[$this->entityName]['has_auto_submit_button']) ? $this->configuration[$this->entityName]['has_auto_submit_button'] : true;
	}

	public function getTemplateForm(): ?string
	{
		return isset($this->_form['template']) ? $this->_form['template'] : null;
	}
}
