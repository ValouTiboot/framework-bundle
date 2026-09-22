<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

final class ButtonFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'button';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return ButtonType::class;
    }

    public function getFormOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $options = parent::getFormOptions($field, $context);

        unset($options['required'], $options['help'], $options['data']);

        return $options;
    }
}
