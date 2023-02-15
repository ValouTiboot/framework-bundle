<?php

namespace Digitix\FrameworkBundle\Helper;

use Digitix\FrameworkBundle\Helper\Helper;
use Digitix\FrameworkBundle\ORM\Paginator;
use Digitix\FrameworkBundle\Sorter\Sorter;
use Digitix\FrameworkBundle\Utils\ToolString;
use Digitix\FrameworkBundle\Helper\HelperListInterface;

class HelperList extends Helper implements HelperListInterface
{
	private $filters;
	private $paginator;
	private $sorter;
	private $defaultActions = ['view', 'edit', 'delete'];
	private $defaultToolbar = ['add','backup'];
	protected $template = '@DigitixFramework/admin/helper/list/list';

	public function generateList()
	{
		$this->setTemplateVars();
		return $this->generate();
	}

	public function setTemplateVars()
	{
    	$vars = [
            'controller_name' => $this->entityName,
            'entity_name' => ToolString::camelToNurl($this->entityName),
            'entities' => $this->paginator->getResults(),
            'sorter' => $this->getSorter(),
            'actions' => $this->getActions(),
            'toolbar' => $this->getToolbar(),
            'list_fields' => $this->getListFields(),
            'pagination' => $this->paginator,
            'filters_form' => $this->filters->createView(),
            'total_items' => $this->getTotal(),
            'has_create' => $this->getHasCreate(),
            'header_link' => $this->getHeaderLink(),
            'sortable' => $this->isDraggable(),
        ];

        $this->tplVars = array_merge($vars, $this->tplVars);

		return $this;
	}

	public function setParameters(array $parameters): HelperList
	{
		if (isset($parameters['list'])) {
			$this->parameters = $parameters['list'];
		}

		return $this;
	}

	public function setPaginator($paginator): HelperList
	{
		$this->paginator = $paginator
			->build(
				$this->getListFields(),
				$this->getSorter()
			)
			->paginate()
		;

		return $this;
	}

	public function setFormFilters($filters): HelperList
	{
		$this->filters = $filters;
		return $this;
	}

	public function setSorter($sorter): HelperList
	{
		$this->sorter = $sorter
			->build($this->getListFields())
		;

		return $this;
	}

	public function getActions(): array
	{
		if (isset($this->parameters['actions']) && count($this->parameters['actions'])) {
			$actions = [];
			foreach ($this->parameters['actions'] as $action) {
				if (in_array($action, $this->defaultActions)) {
					$actions[] = $action;
				}
			}

			return $actions;
		}

		return [];
	}

	public function getListFields(): array
	{
		if (isset($this->parameters['fields'])) {
			return $this->parameters['fields'];
		}

		return [];
	}

	public function getToolbar(): array
	{
		if (isset($this->parameters['toolbar']) && count($this->parameters['toolbar'])) {
			$toolbar = [];
			foreach($this->parameters['toolbar'] as $tool) {
				if (in_array($tool, $this->defaultToolbar)) {
					$toolbar[] = $tool;
				}
			}

			return $toolbar;
		}

		return [];
	}

	public function getHasCreate(): bool
	{
		if (isset($this->parameters['has_create'])
			&& $this->parameters['has_create']
		) {
			return $this->parameters['has_create'];
		}

		return false;
	}

	public function getHeaderLink(): array
	{
		if (isset($this->parameters['header_link'])) {
			return $this->parameters['header_link'];
		}

		return [];
	}

	public function getTotal(): int
	{
		if ($this->paginator->getTotal()) {
			return $this->paginator->getTotal();
		}

		return (int)0;
	}

	public function getSorter(): Sorter
	{
		return $this->sorter;
	}

	public function isDraggable(): bool
	{
		if (isset($this->parameters['sortable'])
			&& $this->parameters['sortable']
		) {
			return $this->parameters['sortable'];
		}

		return false;
	}
}
