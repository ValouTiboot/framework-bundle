<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Sorter\Sorter;
use Digitix\FrameworkBundle\ORM\Paginator;
use Digitix\FrameworkBundle\Helper\HelperList;

interface HelperListInterface
{
	public function generateList();

	public function setActions(array $actions): HelperList;

	public function setTotal($total): HelperList;

	public function setToolbar(array $toolbar): HelperList;

	public function setHasCreate($hasCreate): HelperList;

	public function setFieldsList($fieldsList): HelperList;

	public function setFilters($filters): HelperList;

	public function setList($list): HelperList;

	public function setPagination(Paginator $pagination): HelperList;

	public function setSorter(Sorter $sorter): HelperList;

	public function getSorter(): Sorter;

	public function setSortable(bool $sortable = false): HelperList;
}
