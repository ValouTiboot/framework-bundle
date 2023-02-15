<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Factory\FormFactory;
use Digitix\FrameworkBundle\Factory\SorterFactory;
use Digitix\FrameworkBundle\Factory\PaginatorFactory;
use Digitix\FrameworkBundle\Helper\HelperListInterface;

final class HelperListFactory
{
	private $formFactory;
	private $helperList;
	private $paginatorFactory;
	private $sorterFactory;

	public function __construct(
		FormFactory $formFactory,
		HelperListInterface $helperList,
		PaginatorFactory $paginatorFactory,
		SorterFactory $sorterFactory
	)
	{
		$this->formFactory = $formFactory;
		$this->helperList = $helperList;
		$this->paginatorFactory = $paginatorFactory;
		$this->sorterFactory = $sorterFactory;
	}

	public function build(array $digitixParameters, $tplVars): HelperListInterface
	{
		$this->helperList
			->setParameters($digitixParameters)
            ->setSorter($this->sorterFactory)
			->setPaginator($this->paginatorFactory)
            ->setFormFilters($this->formFactory->buildFormFilters())
            ->setTplVars($tplVars)
        ;

		return $this->helperList;
	}
}
