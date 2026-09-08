<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field;

use Digitix\FrameworkBundle\Admin\Exception\UnknownTypeException;

final class FieldTypeRegistry
{
    public const TAG = 'dgtx.admin.field_type';

    /** @var array<string, FieldTypeInterface> */
    private array $types = [];

    /**
     * @param iterable<string|int, FieldTypeInterface> $types
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

    public function get(string $name): FieldTypeInterface
    {
        return $this->types[$name] ?? throw new UnknownTypeException('field', $name, array_keys($this->types));
    }

    /** @return string[] */
    public function getNames(): array
    {
        return array_keys($this->types);
    }
}
