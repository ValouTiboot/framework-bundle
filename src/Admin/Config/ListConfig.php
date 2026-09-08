<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

final class ListConfig
{
    /**
     * @param string[]                    $toolbar
     * @param string[]                    $actions
     * @param array<int, array>           $headerLinks
     * @param array<string, FieldConfig>  $fields   keyed by field name
     * @param array<string, FilterConfig> $filters  keyed by filter name
     */
    public function __construct(
        public readonly ?string $template,
        public readonly bool $hasCreate,
        public readonly bool $sortable,
        public readonly int $itemsPerPage,
        public readonly array $toolbar,
        public readonly array $actions,
        public readonly array $headerLinks,
        public readonly array $fields,
        public readonly array $filters,
    ) {
    }

    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    public function getField(string $name): ?FieldConfig
    {
        return $this->fields[$name] ?? null;
    }

    public function getFilter(string $name): ?FilterConfig
    {
        return $this->filters[$name] ?? null;
    }

    /**
     * First field declaring "default_sort", as [fieldName, direction].
     *
     * @return array{0: string, 1: string}|null
     */
    public function getDefaultSort(): ?array
    {
        foreach ($this->fields as $field) {
            if ($field->isSortable() && null !== $field->getDefaultSort()) {
                return [$field->name, $field->getDefaultSort()];
            }
        }

        return null;
    }
}
