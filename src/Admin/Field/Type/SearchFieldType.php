<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field\Type;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\AbstractFieldType;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

final class SearchFieldType extends AbstractFieldType
{
    public static function getTypeName(): string
    {
        return 'search';
    }

    public function getFormType(FieldConfig $field, FieldTypeContext $context): string
    {
        return SearchType::class;
    }
}
