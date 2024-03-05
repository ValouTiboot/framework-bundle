<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Helper\HelperViewInterface;

final class HelperViewFactory
{
	private $helperView;

	public function __construct(HelperViewInterface $helperView)
	{
		$this->helperView = $helperView;
	}

	public function build(array $digitixParameters, array $tplVars): HelperViewInterface
	{
		$this->helperView
			->setParameters($digitixParameters)
			->setTplVars($tplVars)
		;

		return $this->helperView;
	}
}
