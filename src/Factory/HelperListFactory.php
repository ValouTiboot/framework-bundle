<?php

namespace Digitix\FrameworkBundle\Factory;

use Digitix\FrameworkBundle\Config\EntityConfigInterface;
use Digitix\FrameworkBundle\Helper\HelperListInterface;
use Digitix\FrameworkBundle\Orm\Paginator;
use Digitix\FrameworkBundle\Sorter\Sorter;
use Symfony\Component\Form\FormInterface;

final class HelperListFactory
{
	public function __construct(HelperListInterface $helperList)
	{
		$this->helperList = $helperList;
	}

	public function build(EntityConfigInterface $entityConfig, array $listFields, FormInterface $filters, Paginator $paginator, Sorter $sorter, $tplVars): HelperListInterface
	{
		$this->helperList
			->setList($paginator->getResults())
            ->setSorter($sorter)
            ->setFieldsList($listFields)
            ->setToolbar($entityConfig->getToolbar())
            ->setActions($entityConfig->getActions())
            ->setPagination($paginator)
            ->setFilters($filters->createView())
			->setTotal($paginator->getTotal())
            ->setHasCreate($entityConfig->hasCreate())
			->setTemplate($entityConfig->getTemplatelist())
			->setHeaderLink($entityConfig->getHeaderLink())
			->setSortable($entityConfig->getSortable())
            ->setTplVars($tplVars)
        ;
		return $this->helperList;
	}
}
