<?php

namespace Digitix\FrameworkBundle\Config;

interface EntityConfigInterface
{
	public function getConfiguration(): array;

	public function getListFields(): array;

	public function getFiltersFields(): array;

	public function getActions(): array;

	public function hasCreate(): bool;

	public function getToolbar(): array;

	public function getTemplatelist();

	public function getSortable(): bool;

	public function getHeaderLink(): array;
}
