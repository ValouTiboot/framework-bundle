<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Sorter\Sorter;
use Digitix\FrameworkBundle\Helper\HelperList;

interface HelperListInterface
{
	public function generateList();

	public function setParameters(array $digitixParameters): HelperList;

	public function setPaginator($paginator): HelperList;

	public function setFormFilters($filters): HelperList;

	public function setSorter($sorter): HelperList;

	public function getActions(): array;

	public function getListFields(): array;

	public function getToolbar(): array;

	public function getHasCreate(): bool;

	public function getHeaderLink(): array;

	public function getTotal(): int;

	public function getSorter(): Sorter;

	public function isDraggable(): bool;
}
