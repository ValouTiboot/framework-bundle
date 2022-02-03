<?php

namespace Digitix\FrameworkBundle\Sorter;

use Digitix\FrameworkBundle\Context\Context;
use Digitix\FrameworkBundle\Sorter\Sorter;

interface SorterInterface
{
	public function setOrderBy(string $orderBy): Sorter;

	public function getOrderBy(): string;

	public function setOrderWay(string $orderWay): Sorter;

	public function getOrderWay(): string;

	public function generateLink($fieldName, $direction);

	public function getContext(): Context;
}
