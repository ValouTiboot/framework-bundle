<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

final class TextFieldType extends AbstractFieldType
{
    /** Fits the usual VARCHAR(255) column. */
    public const MAX_LENGTH = 255;

    public static function getTypeName(): string
    {
        return 'text';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return TextType::class;
    }

    protected function getImplicitConstraints(FieldConfig $field, FieldTypeContext $context): array
    {
        return [...parent::getImplicitConstraints($field, $context), new Length(max: self::MAX_LENGTH)];
    }
}
