<?php

namespace Digitix\FrameworkBundle\Factory;

use Symfony\Component\Form\FormInterface;
use Digitix\FrameworkBundle\Helper\HelperFormInterface;
use Digitix\FrameworkBundle\Config\AdminFormConfigInterface;

final class HelperFormFactory
{
	private $helperForm;

	public function __construct(HelperFormInterface $helperForm)
	{
		$this->helperForm = $helperForm;
	}

	public function build(array $digitixParameters, FormInterface $form, array $tplVars)
	{
		return $this->helperForm
			->setParameters($digitixParameters)
			->setForm($form)
			->setTplVars($tplVars)
		;
	}

	public function buildMulti(array $digitixParameters, array $forms, array $tplVars)
	{
		return $this->helperForm
			->setParameters($digitixParameters)
			->setForms($forms)
			->setTplVars($tplVars)
		;
	}
}
