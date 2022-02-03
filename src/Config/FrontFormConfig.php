<?php

namespace Digitix\FrameworkBundle\Config;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FrontFormConfig implements FrontFormConfigInterface
{
	private $configuration;
	private $entityName;

	public function __construct(ParameterBagInterface $params)
	{
		$this->configuration = $params->get('dgtx_entities');
	}

	public function setEntityName($entityName)
	{
		$this->entityName = $entityName;
		return $this;
	}

	public function getFormFieldsConfig(): array
	{
		return isset($this->configuration[$this->entityName]['fields']) ? $this->configuration[$this->entityName]['fields'] : [];
	}

	public function getTranslationDomain(): string
	{
		return isset($this->configuration[$this->entityName]['translation_domain']) ? $this->configuration[$this->entityName]['translation_domain'] : 'Messages';
	}

	public function getHasAutoSubmitButton(): bool
	{
		return isset($this->configuration[$this->entityName]['has_auto_submit_button']) ? $this->configuration[$this->entityName]['has_auto_submit_button'] : true;
	}
}
