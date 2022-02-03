<?php

namespace Digitix\FrameworkBundle\Filter;

trait FilterTrait
{
	private $label;
	private $name;
	private $alias = 'a';
	private $formType;
	private $options = ['required' => false];
	private $attributes = [];
	private $operator;

	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setFormType($formType)
	{
		$this->formType = $formType;
		return $this;
	}

	public function getFormType()
	{
		return $this->formType;
	}

	public function setOperator($operator)
	{
		$this->operator = $operator;
		return $this;
	}

	public function getOperator()
	{
		return $this->operator;
	}

	public function addOption($name, $value)
	{
		$this->options[$name] = $value;
		return $this;
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function addAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getFormFilterOptions()
	{
		return array_merge($this->options, ['attr' => $this->getAttributes()]);
	}

	public function setAlias(string $alias): self
	{
		$this->alias = $alias;
		return $this;
	}

	public function getAlias()
	{
		return $this->alias;
	}
}
