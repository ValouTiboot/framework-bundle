<?php

namespace Digitix\FrameworkBundle\Config;

interface FieldConfigInterface
{
	public function getFormFieldsConfig(): array;

	public function getHasReturnLink(): bool;

	public function getTranslationDomain(): string;

	public function getHasAutoSubmitButton(): bool;

	public function getTemplateForm(): ?string;
}