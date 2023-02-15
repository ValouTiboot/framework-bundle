<?php

namespace Digitix\FrameworkBundle\Factory;

use Symfony\Component\Form\FormInterface;
use Digitix\FrameworkBundle\Helper\HelperFormInterface;
use Digitix\FrameworkBundle\Config\AdminFormConfigInterface;

final class HelperFormFactory
{
	private $helperForm;
	private $adminFormConfig;
	private $fieldFactory;
	private $formFactory;

	public function __construct(
		HelperFormInterface $helperForm,
		AdminFormConfigInterface $adminFormConfig,
		FieldFactory $fieldFactory,
		FormFactory $formFactory
	)
	{
		$this->helperForm = $helperForm;
		$this->adminFormConfig = $adminFormConfig;
		$this->fieldFactory = $fieldFactory;
		$this->formFactory = $formFactory;
	}

	public function build(FormInterface $form, array $tplVars)
	{
		return $this->helperForm
			->setForm($form)
			->setTplVars($tplVars)
			->setHasReturnLink($this->adminFormConfig->getHasReturnLink())
			->setFormFields($fieldConfig->getFormFieldsConfig())
			->setTemplate($this->adminFormConfig->getTemplateForm())
			->setUploadFields($fieldConfig->getFilesName())
		;
	}

	public function buildMulti(array $forms, array $tplVars)
	{
		return $this->helperForm
			->setForms($forms)
			->setTplVars($tplVars)
		;
	}
}
