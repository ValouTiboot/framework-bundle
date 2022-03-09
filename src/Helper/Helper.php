<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Provider\ContextProvider;

class Helper
{
	protected $entityName;
	protected $tplVars = [];
	protected $template;

	public function __construct(ContextProvider $context)
	{
		dump($context);
		$this->context = $context->getContext();
		$this->entityName = $this->context->getEntityName();
	}

	public function generate()
	{
		return $this->context->getTwig()->render($this->template.'.twig', $this->tplVars);
	}

	public function setTplVars(array $tplVars = []): self
	{
		$this->tplVars = $tplVars;
		return $this;
	}

	public function setTemplate($template = null): self
	{
		if (!is_null($template)) {
			$this->template = $template;
		}

		return $this;
	}
}
