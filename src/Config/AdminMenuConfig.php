<?php

namespace Digitix\FrameworkBundle\Config;

use Digitix\FrameworkBundle\Config\AdminMenuConfigInterface;

class AdminMenuConfig implements AdminMenuConfigInterface
{
	private $configuration;

	public function __construct(array $params)
	{
		$this->configuration = $params['admin_menu'];
	}

	public function getAdminMenuConfig(): array
	{
		return $this->configuration;
	}
}
