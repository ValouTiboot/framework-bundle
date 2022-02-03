<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Sorter\Sorter;
use Digitix\FrameworkBundle\ORM\Paginator;
use Digitix\FrameworkBundle\Helper\Helper;
use Digitix\FrameworkBundle\Helper\HelperListInterface;
use Digitix\FrameworkBundle\Utils\ToolString;

class HelperList extends Helper implements HelperListInterface
{
	private $_list;
	private $sorter;
	private $defaultActions = ['view', 'edit', 'delete'];
	private $actions = [];
	private $defaultToolbar = ['add','backup'];
	private $toolbar = [];
	private $fieldsList;
	private $pagination;
	private $filters;
	private $total = 0;
	private $hasCreate = true;
	private $headerLink = [];
	private $sortable = false;
	protected $template = '@DigitixFramework/admin/helper/list/list';

	public function generateList()
	{
    	$vars = [
            'controller_name' => $this->entityName,
            'entity_name' => ToolString::camelToNurl($this->entityName),
            'entities' => $this->_list,
            'sorter' => $this->sorter,
            'actions' => $this->actions,
            'toolbar' => $this->toolbar,
            'list_fields' => $this->fieldsList,
            'pagination' => $this->pagination,
            'filters_form' => $this->filters,
            'total_items' => $this->total,
            'has_create' => $this->hasCreate,
            'header_link' => $this->headerLink,
            'sortable' => $this->sortable,
        ];

        $this->tplVars = array_merge($vars, $this->tplVars);

		return $this->generate();
	}

	public function setActions(array $actions): HelperList
	{
		if (count($actions)) {
			foreach ($actions as $action) {
				if (in_array($action, $this->defaultActions))
				$this->actions[] = $action;
			}
		}

		return $this;
	}

	public function setTotal($total): HelperList
	{
		$this->total = $total;
		return $this;
	}

	public function setToolbar(array $toolbar): HelperList
	{
		if (count($toolbar)) {
			foreach($toolbar as $tool) {
				if (in_array($tool, $this->defaultToolbar))
				$this->toolbar[] = $tool;
			}
		}

		return $this;
	}

	public function setHasCreate($hasCreate): HelperList
	{
		$this->hasCreate = $hasCreate;
		return $this;
	}

	public function setHeaderLink($headerLink): HelperList
	{
		$this->headerLink = $headerLink;
		return $this;
	}

	public function setFieldsList($fieldsList): HelperList
	{
		$this->fieldsList = $fieldsList;
		return $this;
	}

	public function setFilters($filters): HelperList
	{
		$this->filters = $filters;
		return $this;
	}

	public function setList($list): HelperList
	{
		$this->_list = $list;
		return $this;
	}

	public function setPagination(Paginator $pagination): HelperList
	{
		$this->pagination = $pagination;
		return $this;
	}

	public function setSorter(Sorter $sorter): HelperList
	{
		$this->sorter = $sorter;
		return $this;
	}

	public function getSorter(): Sorter
	{
		return $this->sorter;
	}

	public function setSortable(bool $sortable = false): HelperList
	{
		$this->sortable = $sortable;
		return $this;
	}
}
