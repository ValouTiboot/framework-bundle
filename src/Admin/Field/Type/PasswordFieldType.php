<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Digitix\FrameworkBundle\Admin\Form\HashedPasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Two password inputs that must match; the value is hashed into the bound
 * user's password when submitted (see HashedPasswordType).
 */
final class PasswordFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'password';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return RepeatedType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        return [
            'type' => HashedPasswordType::class,
            'first_name' => $field->name,
            'second_name' => 'repeat_'.$field->name,
            'first_options' => ['label' => $field->label],
            'second_options' => ['label' => $field->label.'Repeat'],
            'options' => ['user' => $context->data],
            'invalid_message' => 'form.error.password_mismatch',
        ];
    }
}
