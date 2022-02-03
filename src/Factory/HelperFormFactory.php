<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Config\FieldConfig;
use Digitix\FrameworkBundle\Helper\HelperFormInterface;
use Symfony\Component\Form\FormInterface;

class HelperFormFactory
{
	public function __construct(HelperFormInterface $helperForm)
	{
		$this->helperForm = $helperForm;
	}

	public function build(FieldConfig $fieldConfig, FormInterface $form, array $tplVars)
	{
		return $this->helperForm
			->setForm($form)
			->setTplVars($tplVars)
			->setHasReturnLink($fieldConfig->getHasReturnLink())
			->setTemplate($fieldConfig->getTemplateForm())
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
