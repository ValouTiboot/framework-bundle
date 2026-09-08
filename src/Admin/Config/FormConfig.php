<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

final class FormConfig
{
    /**
     * @param array<string, FieldConfig> $fields keyed by form field name
     */
    public function __construct(
        public readonly ?string $template,
        public readonly bool $hasReturnLink,
        public readonly bool $hasAutoSubmitButton,
        public readonly string $translationDomain,
        public readonly array $fields,
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

    /**
     * @return array<string, FieldConfig> fields of the given type, keyed by name
     */
    public function getFieldsOfType(string $type): array
    {
        return array_filter($this->fields, static fn (FieldConfig $field) => $field->type === $type);
    }
}
