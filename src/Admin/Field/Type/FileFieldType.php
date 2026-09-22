<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * File upload. The input is not mapped: after a valid submit, UploadHandler
 * moves the file under public/uploads/{entity}/{field}/ and stores the file
 * name into the entity property.
 */
final class FileFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'file';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return FileType::class;
    }

    protected function getTypeOptions(FieldConfig $field, FieldTypeContext $context): array
    {
        return [
            'mapped' => false,
            'required' => (bool) $field->get('required', false),
        ];
    }
}
