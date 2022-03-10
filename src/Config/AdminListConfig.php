<?php

namespace Digitix\FrameworkBundle\Config;

use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\Config\AdminListConfigInterface;

class AdminListConfig implements AdminListConfigInterface
{
	private $context;
	private $entityName;
	private $configuration;
	private $_list = [];

	public function __construct(array $params, ContextProvider $context)
	{
		$this->context = $context->getContext();
		$this->entityName = $this->context->getEntityName();
		$this->configuration = $params['admin_entities'][$this->entityName];

		if (isset($this->configuration['list']) && count($this->configuration['list'])) {
			$this->_list = $this->configuration['list'];
		}
	}

	public function getConfiguration(): array
	{
		return $this->configuration;
	}

	public function getListFields(): array
	{
		return isset($this->_list['fields']) ? $this->_list['fields'] : [];
	}

	public function getFiltersFields(): array
	{
		return isset($this->_list['filters']) ? $this->_list['filters'] : [];
	}

	public function getActions(): array
	{
		return isset($this->_list['actions']) ? $this->_list['actions'] : [];
	}

	public function hasCreate(): bool
	{
		return isset($this->_list['has_create']) ? $this->_list['has_create'] : [];
	}

	public function getHeaderLink(): array
	{
		return isset($this->_list['header_link']) ? $this->_list['header_link'] : [];
	}

	public function getToolbar(): array
	{
		return isset($this->_list['toolbar']) ? $this->_list['toolbar'] : [];
	}

	public function getTemplatelist()
	{
		return isset($this->_list['template']) ? $this->_list['template'] : null;
	}

	public function getSortable(): bool
	{
		return isset($this->_list['sortable']) ? $this->_list['sortable'] : false;
	}
}
