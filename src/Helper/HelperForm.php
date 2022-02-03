<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Helper\Helper;
use Digitix\FrameworkBundle\Helper\HelperFormInterface;
use Digitix\FrameworkBundle\Utils\ToolString;
use Symfony\Component\Form\FormInterface;

class HelperForm extends Helper implements HelperFormInterface
{
	private $form;
	private $forms;
	private $hasReturnLink = true;
	protected $template = '@DigitixFramework/admin/helper/form/create';

	public function generateForm()
	{
		$vars = [
            'controller_name' => $this->entityName,
            'entity_name' => ToolString::camelToNurl($this->entityName),
            'has_return_link' => $this->hasReturnLink,
            'form' => $this->form,
            'forms' => $this->forms,
        ];

		$this->tplVars = array_merge($vars, $this->tplVars);

		return $this->generate();
	}

	public function setForm(FormInterface $form): self
	{
		$this->form = $form->createView();
		return $this;
	}

	public function setHasReturnLink($hasReturnLink)
	{
		$this->hasReturnLink = $hasReturnLink;
		return $this;
	}

	public function setForms(array $forms): self
	{
		foreach ($forms as  $key => $form) {
			$this->forms[$key] = $form->createView();
		}

		return $this;
	}
}
