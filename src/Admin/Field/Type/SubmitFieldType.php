<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class SubmitFieldType extends AbstractFieldType
{
    public const DEFAULT_LABEL = 'form.default.submit';
    public const DEFAULT_DOMAIN = 'Admin.Form.Default';

    public static function getTypeName(): string
    {
        return 'submit';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return SubmitType::class;
    }

    public function getFormOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        $options = parent::getFormOptions($field, $context);

        unset($options['required'], $options['help'], $options['data']);

        $options['label'] ??= self::DEFAULT_LABEL;

        return $options;
    }

    /**
     * Definition of the button added automatically at the end of a form.
     *
     * @return array{name: string, type: string, options: array<string, mixed>}
     */
    public static function defaultDefinition(): array
    {
        return [
            'name' => 'save',
            'type' => SubmitType::class,
            'options' => ['label' => self::DEFAULT_LABEL, 'translation_domain' => self::DEFAULT_DOMAIN],
        ];
    }
}
