<?php

namespace Digitix\FrameworkBundle\Filter;

interface FilterInterface
{
	public static function getInstance($name, $filter, $context);
}
