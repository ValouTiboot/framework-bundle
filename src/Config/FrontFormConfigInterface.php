<?php

namespace Digitix\FrameworkBundle\Config;

interface FrontFormConfigInterface
{
	public function getFormFieldsConfig(): array;

	public function getTranslationDomain(): string;

	public function getHasAutoSubmitButton(): bool;
}