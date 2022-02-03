<?php

namespace Digitix\FrameworkBundle\Presenter;

final class EntityPresenter
{
	private $name;
	private $fqcn;
	private $instance;
	private $primaryKeyValue = null;

	public function setName($name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setFqcn($fqcn): self
	{
		$this->fqcn = $fqcn;
		return $this;
	}

	public function getFqcn(): string
	{
		return $this->fqcn;
	}

	public function setInstance($instance): self
	{
		$this->instance = $instance;
		return $this;
	}

	public function getInstance(): object
	{
		return $this->instance;
	}

	public function setPrimaryKeyValue($primaryKeyValue): self
	{
		$this->primaryKeyValue = $primaryKeyValue;
		return $this;
	}

	public function getPrimaryKeyValue()
	{
		return $this->primaryKeyValue;
	}
}
