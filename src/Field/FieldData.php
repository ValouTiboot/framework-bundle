<?php

namespace Digitix\FrameworkBundle\Field;

final class FieldData
{
	private $_elements;

	public function __construct($_elements)
	{
		$this->_elements = $_elements->toArray();
	}

	public function __get($name)
	{
		return $this->getDataElement($name);
	}

	public function __set($name, $value)
	{
		$field = $this->_elements[$name];
		$field->addOption('data', $value);
		return $field;
	}

	private function getDataElement($name)
	{
		$field = $this->_elements[$name];
		return $field->getOption('data');
	}

	public function toArray()
	{
		$array = [];
		foreach ($this->_elements as $name => $field)
			$array[$name] = $field->getOption('data');

		return $array;
	}

}