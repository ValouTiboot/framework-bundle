<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Yes/no switch rendered as two radios (styled by admin.js on ".dgtx-switch").
 */
final class BoolFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'bool';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return ChoiceType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $options = [
            'choices' => ['yes' => true, 'no' => false],
            'choice_label' => static fn ($choice, string $key): string => 'label.default.'.$key,
            'choice_translation_domain' => $context->translationDomain,
            'expanded' => true,
            'multiple' => false,
            'attr' => ['class' => 'dgtx-switch'],
        ];

        if ($this->needsPlaceholder($field)) {
            $options['placeholder'] = '';
        }

        return $options;
    }
}
