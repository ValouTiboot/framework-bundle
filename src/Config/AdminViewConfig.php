<?php

namespace Digitix\FrameworkBundle\Config;

use Digitix\FrameworkBundle\Config\ViewConfigInterface;
use Digitix\FrameworkBundle\Provider\ContextProvider;

class AdminViewConfig implements AdminViewConfigInterface
{
	private $context;
	private $entityName;
	private $configuration;
	private $_view = [];

	public function __construct(array $params, ContextProvider $context)
	{
		$this->context = $context->getContext();
		$this->entityName = $this->context->getEntityName();
		$this->configuration = $params['admin_entities'][(string) $this->entityName];

		if (isset($this->configuration['view']) && count($this->configuration['view'])) {
			$this->_view = $this->configuration['view'];
		}
	}

	public function getTemplate(): ?string
	{
		return isset($this->_view['template']) && !is_null($this->_view['template']) ? $this->_view['template'] : null;
	}
}
