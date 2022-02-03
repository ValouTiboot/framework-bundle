<?php

namespace Digitix\FrameworkBundle\Entity;

final class Magic
{
	public function __set($name, $value)
	{
		$this->{$name} = $value;
		return $this;
	}

	public function __get($name)
	{
		return $this->{$name} ?? null;
	}
}
