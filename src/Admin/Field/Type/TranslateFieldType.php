<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * One input per language for a translated property. The form child is bound
 * to the magic "translatableXxx" accessor of Translatable entities, which
 * exposes a [languageId => value] map.
 *
 * "collectionType" is the Symfony form type of each entry (TextType by default).
 */
final class TranslateFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'translate';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return CollectionType::class;
    }

    public function getFormOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $options = parent::getFormOptions($field, $context);

        // attributes belong to each entry, not to the collection wrapper
        $attr = $options['attr'] ?? [];
        unset($options['attr'], $options['data']);

        $entryType = (string) $field->get('collectionType', TextType::class);
        if (!class_exists($entryType)) {
            throw new \InvalidArgumentException(sprintf('Field "%s": collectionType "%s" does not exist.', $field->name, $entryType));
        }

        $options['entry_type'] = $entryType;
        $options['entry_options'] = array_filter([
            'attr' => $attr,
            'required' => $options['required'] ?? null,
            'label' => false,
        ], static fn ($value) => null !== $value);
        $options['allow_add'] = false;
        $options['allow_delete'] = false;

        return $options;
    }
}
