<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Config\ViewConfigInterface;
use Digitix\FrameworkBundle\Helper\HelperViewInterface;

final class HelperViewFactory
{
	public function __construct(HelperViewInterface $helperView)
	{
		$this->helperView = $helperView;
	}

	public function build(ViewConfigInterface $viewConfig, $tplVars): HelperViewInterface
	{
		$this->helperView
			->setTemplate($viewConfig->getTemplate())
            ->setTplVars([]) // add extra tpl vars;
        ;

		return $this->helperView;
	}
}
