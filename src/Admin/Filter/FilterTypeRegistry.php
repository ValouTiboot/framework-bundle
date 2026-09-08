<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter;

use Digitix\FrameworkBundle\Admin\Exception\UnknownTypeException;

final class FilterTypeRegistry
{
    public const TAG = 'dgtx.admin.filter_type';

    /** @var array<string, FilterTypeInterface> */
    private array $types = [];

    /**
     * @param iterable<string|int, FilterTypeInterface> $types
     */
    public function __construct(iterable $types)
    {
        foreach ($types as $name => $type) {
            $this->types[\is_string($name) ? $name : $type::getTypeName()] = $type;
        }
    }

    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }

    public function get(string $name): FilterTypeInterface
    {
        return $this->types[$name] ?? throw new UnknownTypeException('filter', $name, array_keys($this->types));
    }

    /** @return string[] */
    public function getNames(): array
    {
        return array_keys($this->types);
    }
}
