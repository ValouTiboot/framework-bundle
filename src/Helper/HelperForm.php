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
	protected $template = '@DigitixFramework/admin/helper/form/_partials/form.html';

	public function generateForm()
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
            'hasReturnLink' => $this->hasReturnLink(),
			'fields' => $this->getFields(),
			'uploadfields' => $this->getUploadFields(),
            'form' => $this->form,
            'forms' => $this->forms,
        ];

		$this->tplVars = array_merge($vars, $this->tplVars);

		return $this;
	}

	public function setParameters(array $parameters): HelperForm
	{
		if (isset($parameters['form'])) {
			$this->parameters = $parameters['form'];
		}

		return $this;
	}

	public function hasReturnLink(): bool
	{
		if (isset($this->parameters['has_return_link'])) {
			return $this->parameters['has_return_link'];
		}

		return true;
	}

	public function getTranslationDomain(): string
	{
		if (isset($this->parameters['translation_domain'])) {
			return $this->parameters['translation_domain'];
		}

		return 'Admin.Fields.Label';
	}

	public function hasAutoSubmitButton(): bool
	{
		if (isset($this->parameters['has_auto_submit_button'])) {
			return $this->parameters['has_auto_submit_button'];
		}

		return true;
	}

	public function hasUploadFields(): bool
	{
		return isset($this->parameters['files'])
			&& is_array($this->parameters['files'])
			&& count($this->parameters['files'])
		;
	}

	public function getUploadFields(): array
	{
		if (isset($this->parameters['files'])) {
			return $this->parameters['files'];
		}

		return [];
	}

	private function getFields(): array
	{
		if (isset($this->parameters['fields'])) {
			return $this->parameters['fields'];
		}

		return [];
	}

	public function setForm(FormInterface $form): HelperForm
	{
		$this->form = $form->createView();
		return $this;
	}

	public function setForms(array $forms): HelperForm
	{
		foreach ($forms as  $key => $form) {
			$this->forms[$key] = $form->createView();
		}

		return $this;
	}
}
