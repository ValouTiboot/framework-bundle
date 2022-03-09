<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Config\EntityConfigInterface;
use Digitix\FrameworkBundle\Sorter\SorterInterface;
use Digitix\FrameworkBundle\Provider\ContextProvider;

class SorterFactory
{
	private $sorter;
	private $context;
	private $entityConfig;

	public function __construct(ContextProvider $contextProvider, EntityConfigInterface $entityConfig, SorterInterface $sorter)
	{
		$this->context = $contextProvider->getContext();
		$this->entityConfig = $entityConfig;
		$this->sorter = $sorter;
	}

	public function build(): SorterInterface
	{
		$orderBy = '';
		$orderWay = '';
		$listFields = $this->entityConfig->getListFields();

		if ($listFields) {
			foreach ($listFields as $fieldName => $value) {
				if (isset($value['sort']) && $value['sort'] && isset($value['default_sort'])) {
					$orderBy = $fieldName;
					$orderWay = $value['default_sort'];
					break;
				}
			}
		}

		$params = $this->context->getRequest()->query->all();

		if (isset($params['sortBy']) && in_array($params['sortBy'], array_keys($listFields))) {
			$orderBy = $params['sortBy'];

			if (isset($params['sortWay'])) {
				$orderWay = $params['sortWay'];
			}
		}

		return $this->sorter
			->setOrderBy($orderBy)
			->setOrderWay($orderWay)
		;
	}
}