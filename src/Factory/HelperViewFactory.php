<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Config\ViewConfigInterface;
use Digitix\FrameworkBundle\Helper\HelperViewInterface;

final class HelperViewFactory
{
	private $helperView;
	private $viewConfig;

	public function __construct(HelperViewInterface $helperView, ViewConfigInterface $viewConfig)
	{
		$this->helperView = $helperView;
		$this->viewConfig = $viewConfig;
	}

	public function build($tplVars): HelperViewInterface
	{
		$this->helperView
			->setTplVars($tplVars)
			->setTemplate($this->viewConfig->getTemplate())
        ;

		return $this->helperView;
	}
}
