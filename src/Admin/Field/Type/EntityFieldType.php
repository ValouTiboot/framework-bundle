<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Digitix\FrameworkBundle\Admin\Persistence\EntityClassResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Relation to another entity. The target class is given by
 * "collection.name" (an entity name, resolved like admin entities) and
 * defaults to the field name; "collection.label" / "collection.value" map to
 * choice_label / choice_value.
 */
final class EntityFieldType extends AbstractFieldType
{
    public function __construct(private readonly EntityClassResolver $classResolver)
    {
    }

    public static function getTypeName(): string
    {
        return 'entity';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return EntityType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $collection = (array) $field->get('collection', []);

        $options = [
            'class' => $this->classResolver->resolve((string) ($collection['name'] ?? $field->property)),
            'choice_label' => (string) ($collection['label'] ?? 'name'),
            'choice_value' => (string) ($collection['value'] ?? 'id'),
        ];

        foreach (['expanded', 'multiple'] as $name) {
            if ($field->has($name)) {
                $options[$name] = (bool) $field->get($name);
            }
        }

        if ($this->needsPlaceholder($field)) {
            $options['placeholder'] = '';
        }

        return $options;
    }
}
