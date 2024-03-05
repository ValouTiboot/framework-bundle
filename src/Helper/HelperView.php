<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Helper\Helper;
use Digitix\FrameworkBundle\Helper\HelperViewInterface;
use Digitix\FrameworkBundle\Utils\ToolString;

class HelperView extends Helper implements HelperViewInterface
{
	protected $template = '@DigitixFramework/admin/helper/view/view.html';

	public function generateView()
	{
		return $this
			->setTemplateVars()
			->generate()
		;
	}

	public function setTemplateVars()
	{
    	$vars = [
            'controllerName' => $this->entityName,
            'entityName' => ToolString::camelToNurl($this->entityName),
        ];

        $this->tplVars = array_merge($vars, $this->tplVars);

		return $this;
	}

	public function setParameters(array $parameters): HelperView
	{
		if (isset($parameters['view'])) {
			$this->parameters = $parameters['view'];
		}

		return $this;
	}
}
