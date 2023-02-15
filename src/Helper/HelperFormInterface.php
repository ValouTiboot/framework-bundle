<?php

namespace Digitix\FrameworkBundle\Helper;

use Symfony\Component\Form\FormInterface;

interface HelperFormInterface
{
	public function generateForm();

	public function setForm(FormInterface $form): HelperForm;

	public function setHasReturnLink($hasReturnLink);

	public function setForms(array $forms): HelperForm;
}
