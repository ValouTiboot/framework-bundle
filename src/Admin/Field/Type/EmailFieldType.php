<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

final class EmailFieldType extends AbstractFieldType
{
    /** Same limit as the Symfony maker for e-mail columns. */
    public const MAX_LENGTH = 180;

    public static function getTypeName(): string
    {
        return 'email';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return EmailType::class;
    }

    protected function getImplicitConstraints(FieldConfig $field, FieldTypeContext $context): array
    {
        return [...parent::getImplicitConstraints($field, $context), new Email(), new Length(max: self::MAX_LENGTH)];
    }
}
