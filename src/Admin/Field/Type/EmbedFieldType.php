<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Embedded records of a one-to-many relation, edited inside the parent form:
 * a CollectionType whose entries use the project form type "entryType".
 *
 *   items:
 *     type: embed
 *     entryType: App\Form\ExpertiseItemType
 *
 * Entries can be added, removed and reordered (admin.js, block
 * dgtx_embed_widget). The relation needs addXxx() / removeXxx() on the
 * parent (by_reference is off), and the entry type creates its own record,
 * translations included, for a new entry.
 */
final class EmbedFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'embed';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return CollectionType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $entryType = (string) $field->get('entryType', '');
        if (!class_exists($entryType)) {
            throw new \InvalidArgumentException(sprintf('Field "%s": entryType "%s" does not exist.', $field->name, $entryType));
        }

        return [
            'entry_type' => $entryType,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'block_prefix' => 'dgtx_embed',
        ];
    }

    /** The entries carry their own constraints; an empty collection is allowed. */
    protected function getImplicitConstraints(FieldConfig $field, FieldTypeContext $context): array
    {
        return [];
    }
}
