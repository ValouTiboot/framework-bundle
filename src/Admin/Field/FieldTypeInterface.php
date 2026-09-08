<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Maps a YAML field type ("text", "entity", "translate"...) to a Symfony form
 * type and its options.
 *
 * Implementations are services; any class implementing this interface is
 * registered automatically. Projects can add their own types the same way.
 */
#[AutoconfigureTag(FieldTypeRegistry::TAG)]
interface FieldTypeInterface
{
    /** Name used in the YAML "type" key. */
    public static function getTypeName(): string;

    /** @return class-string<\Symfony\Component\Form\FormTypeInterface<mixed>> */
    public function getFormType(FieldConfig $field, FieldTypeContext $context): string;

    /** @return array<string, mixed> */
    public function getFormOptions(FieldConfig $field, FieldTypeContext $context): array;
}
