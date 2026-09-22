<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

/**
 * One field of a list or of a form.
 *
 * $name is the YAML key: for forms it is the form child name (may be a magic
 * "translatableXxx" accessor), for lists it is only an identifier.
 * $property is the entity property actually read/written ("name" key in YAML,
 * defaults to $name).
 * $options keeps every other YAML key untouched so that field types can
 * define their own options without touching the configuration tree.
 */
final class FieldConfig
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public readonly string $name,
        public readonly string $property,
        public readonly string $type,
        public readonly ?string $label,
        public readonly array $options = [],
    ) {
    }

    /**
     * @param array<string, mixed> $definition raw YAML definition
     */
    public static function fromArray(string $name, array $definition): self
    {
        return new self(
            $name,
            (string) ($definition['name'] ?? $name),
            (string) ($definition['type'] ?? 'text'),
            isset($definition['label']) ? (string) $definition['label'] : null,
            array_diff_key($definition, ['name' => 1, 'type' => 1, 'label' => 1]),
        );
    }

    public function has(string $option): bool
    {
        return \array_key_exists($option, $this->options) && null !== $this->options[$option];
    }

    public function get(string $option, mixed $default = null): mixed
    {
        return $this->options[$option] ?? $default;
    }

    // --- helpers used by templates -------------------------------------------------

    public function isSortable(): bool
    {
        return (bool) ($this->options['sort'] ?? false);
    }

    public function getDefaultSort(): ?string
    {
        $direction = $this->options['default_sort'] ?? null;

        return \is_string($direction) ? strtolower($direction) : null;
    }

    /** Property displayed for "entity" typed list columns (defaults to "name"). */
    public function getColumn(): ?string
    {
        return isset($this->options['column']) ? (string) $this->options['column'] : null;
    }

    public function isRequired(): ?bool
    {
        return isset($this->options['required']) ? (bool) $this->options['required'] : null;
    }
}
