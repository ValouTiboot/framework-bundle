<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Helper\Helper;
use Digitix\FrameworkBundle\Helper\HelperViewInterface;
use Digitix\FrameworkBundle\Utils\ToolString;

class HelperView extends Helper implements HelperViewInterface
{
	protected $template = '@DigitixFramework/admin/helper/view/view';

	public function generateView()
	{
		$vars = [
            'controller_name' => $this->entityName,
            'entity_name' => ToolString::camelToNurl($this->entityName),
        ];

		$this->tplVars = array_merge($vars, $this->tplVars);

		return $this->generate();
	}
}
