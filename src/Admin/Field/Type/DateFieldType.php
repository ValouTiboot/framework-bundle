<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\DateType;

final class DateFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'date';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return DateType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $options = [];

        if ($field->has('years')) {
            $options['years'] = (array) $field->get('years');
        }

        if ($field->has('widget')) {
            $options['widget'] = (string) $field->get('widget');
        }

        return $options;
    }
}
