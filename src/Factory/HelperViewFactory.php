<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Config\AdminViewConfigInterface;
use Digitix\FrameworkBundle\Helper\HelperViewInterface;

final class HelperViewFactory
{
	private $helperView;
	private $adminViewConfig;

	public function __construct(HelperViewInterface $helperView, AdminViewConfigInterface $adminViewConfig)
	{
		$this->helperView = $helperView;
		$this->adminViewConfig = $adminViewConfig;
	}

	public function build($tplVars): HelperViewInterface
	{
		$this->helperView
			->setTplVars($tplVars)
			->setTemplate($this->adminViewConfig->getTemplate())
        ;

		return $this->helperView;
	}
}
