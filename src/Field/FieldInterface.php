<?php

namespace Digitix\FrameworkBundle\Field;

interface FieldInterface
{
	public function setName($name);

	public function getName(): string;

	public function setLabel($label);

	public function getLabel(): string;

	public function setFormType($formType);

	public function getFormtype();

	public function addOption($name, $value);

	public function getOptions(): array;

	public function setAttributes(array $attributes = []);

	public function addAttribute($name, $value);

	public function getAttributes(): array;

	public function getFormOptions(): array;
}
