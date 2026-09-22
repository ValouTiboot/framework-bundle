<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

/**
 * One filter of a list. $name is both the form child name and the entity
 * (or translation) property to filter on.
 *
 * $alias is the DQL alias to filter against: "a" for the entity, "t" for its
 * translation. When null, the alias is guessed from the Doctrine metadata.
 */
final class FilterConfig
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $label,
        public readonly ?string $alias,
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
            (string) ($definition['type'] ?? 'text'),
            isset($definition['label']) ? (string) $definition['label'] : null,
            isset($definition['alias']) ? (string) $definition['alias'] : null,
            array_diff_key($definition, ['type' => 1, 'label' => 1, 'alias' => 1]),
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
}
