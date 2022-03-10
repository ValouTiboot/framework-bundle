<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Factory\FormFactory;
use Digitix\FrameworkBundle\Factory\SorterFactory;
use Digitix\FrameworkBundle\Factory\PaginatorFactory;
use Digitix\FrameworkBundle\Helper\HelperListInterface;
use Digitix\FrameworkBundle\Config\AdminListConfigInterface;

final class HelperListFactory
{
	private $adminListConfig;
	private $formFactory;
	private $helperList;
	private $paginatorFactory;
	private $sorterFactory;

	public function __construct(
		AdminListConfigInterface $adminListConfig,
		FormFactory $formFactory,
		HelperListInterface $helperList,
		PaginatorFactory $paginatorFactory,
		SorterFactory $sorterFactory
	)
	{
		$this->adminListConfig = $adminListConfig;
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
            ->setFieldsList($this->adminListConfig->getListFields())
            ->setToolbar($this->adminListConfig->getToolbar())
            ->setActions($this->adminListConfig->getActions())
            ->setPagination($paginator)
            ->setFilters($this->formFactory->buildFormFilters()->createView())
			->setTotal($paginator->getTotal())
            ->setHasCreate($this->adminListConfig->hasCreate())
			->setHeaderLink($this->adminListConfig->getHeaderLink())
			->setSortable($this->adminListConfig->getSortable())
            ->setTplVars($tplVars)
			->setTemplate($this->adminListConfig->getTemplatelist())
        ;
		return $this->helperList;
	}
}
