<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Sorter\SorterInterface;

class SorterFactory
{
	private $sorter;

	public function __construct(SorterInterface $sorter)
	{
		$this->sorter = $sorter;
	}

	public function build($listFields): SorterInterface
	{
		$orderBy = '';
		$orderWay = '';

		if (count($listFields))
		foreach ($listFields as $fieldName => $value)
		{
			if (isset($value['sort']) && $value['sort'] && isset($value['default_sort']))
			{
				$orderBy = $fieldName;
				$orderWay = $value['default_sort'];
				break;
			}
		}

		$request = $this->sorter->getContext()->getRequest();
		$params = $request->query->all();

		if (isset($params['sortBy']) && in_array($params['sortBy'], array_keys($listFields)))
		{
			$orderBy = $params['sortBy'];

			if (isset($params['sortWay']))
				$orderWay = $params['sortWay'];
		}

		return $this->sorter
			->setOrderBy($orderBy)
			->setOrderWay($orderWay)
		;
	}
}