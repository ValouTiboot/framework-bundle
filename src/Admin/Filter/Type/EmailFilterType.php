<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Filter\Type;

/**
 * Same as "text"; exists so that a filter can share the type name of its field.
 */
final class EmailFilterType extends TextFilterType
{
    public static function getTypeName(): string
    {
        return 'email';
    }
}
