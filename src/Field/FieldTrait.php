<?php

namespace Digitix\FrameworkBundle\Field;

trait FieldTrait
{
	private $label;
	private $name;
	private $formType;
	private $options = [];
	private $attributes = [];

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function setFormType($formType)
	{
		$this->formType = $formType;
		return $this;
	}

	public function getFormtype()
	{
		return $this->formType;
	}

	public function addOption($name, $value)
	{
		$this->options[$name] = $value;
		return $this;
	}

	public function getOption($name)
	{
		return $this->options[$name];
	}

	public function getOptions(): array
	{
		return $this->options;
	}

	public function setAttributes(array $attributes = [])
	{
		$this->attributes = $attributes;
		return $this;
	}

	public function addAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}

	public function getAttributes(): array
	{
		return $this->attributes;
	}

	public function getFormOptions(): array
	{
		return array_merge($this->options, ['attr' => $this->getAttributes()]);
	}
}
