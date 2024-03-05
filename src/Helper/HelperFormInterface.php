<?php

namespace Digitix\FrameworkBundle\Helper;

use Symfony\Component\Form\FormInterface;

interface HelperFormInterface
{
	public function generateForm();

	public function hasReturnLink(): bool;

	public function setForm(FormInterface $form): HelperForm;

	public function setForms(array $forms): HelperForm;
}
