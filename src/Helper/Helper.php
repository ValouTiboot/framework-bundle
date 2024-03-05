<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Provider\ContextProvider;

class Helper
{
	protected $context;
	protected $entityName;
	protected $parameters = [];
	protected $template;
	protected $tplVars = [];
	protected $templateOverride = null;

	public function __construct(ContextProvider $context)
	{
		$this->context = $context->getContext();
		$this->entityName = $this->context->getEntityName();
	}

	public function generate()
	{
		return $this->context->getTwig()
			->render(
				$this->getTemplate().'.twig',
				$this->getTemplateVars()
			)
		;
	}

	public function setTplVars(array $tplVars): self
	{
		$this->tplVars = $tplVars;
		return $this;
	}

	public function getTemplateVars()
	{
		return $this->tplVars;
	}

	public function setTemplateOverride($templateOverride)
	{
		$this->templateOverride = $templateOverride;
		return $this;
	}

	public function getTemplate(): string
	{
		if ($this->templateOverride !== null) {
			$this->template = $this->templateOverride;
		} else if (isset($this->parameters['template'])
			&& $this->parameters['template'] !== null
		) {
			$this->template = $this->parameters['template'];
		}

		return $this->template;
	}
}
