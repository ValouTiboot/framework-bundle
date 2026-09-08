<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class TextareaFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'textarea';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return TextareaType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        return $field->has('rows') ? ['attr' => ['rows' => (int) $field->get('rows')]] : [];
    }
}
