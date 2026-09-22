<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Choices come either from the "choice" map (label => value) or from a
 * "callback" static callable.
 */
final class ChoiceFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'choice';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return ChoiceType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $options = [
            'choices' => $this->resolveChoices($field),
            'choice_translation_domain' => $context->translationDomain,
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
