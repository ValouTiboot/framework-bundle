<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Factory\FormFactory;
use Digitix\FrameworkBundle\Factory\SorterFactory;
use Digitix\FrameworkBundle\Factory\PaginatorFactory;
use Digitix\FrameworkBundle\Helper\HelperListInterface;
use Digitix\FrameworkBundle\Config\EntityConfigInterface;

final class HelperListFactory
{
	private $entityConfig;
	private $formFactory;
	private $helperList;
	private $paginatorFactory;
	private $sorterFactory;

	public function __construct(
		EntityConfigInterface $entityConfig,
		FormFactory $formFactory,
		HelperListInterface $helperList,
		PaginatorFactory $paginatorFactory,
		SorterFactory $sorterFactory
	)
	{
		$this->entityConfig = $entityConfig;
		$this->formFactory = $formFactory;
		$this->helperList = $helperList;
		$this->paginatorFactory = $paginatorFactory;
		$this->sorterFactory = $sorterFactory;
	}

	public function build($tplVars): HelperListInterface
	{
		$paginator = $this->paginatorFactory->build()->paginate();

		$this->helperList
			->setList($paginator->getResults())
            ->setSorter($this->sorterFactory->build())
            ->setFieldsList($this->entityConfig->getListFields())
            ->setToolbar($this->entityConfig->getToolbar())
            ->setActions($this->entityConfig->getActions())
            ->setPagination($paginator)
            ->setFilters($this->formFactory->buildFormFilters()->createView())
			->setTotal($paginator->getTotal())
            ->setHasCreate($this->entityConfig->hasCreate())
			->setHeaderLink($this->entityConfig->getHeaderLink())
			->setSortable($this->entityConfig->getSortable())
            ->setTplVars($tplVars)
			->setTemplate($this->entityConfig->getTemplatelist())
        ;
		return $this->helperList;
	}
}
