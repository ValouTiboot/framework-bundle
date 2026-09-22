<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Field;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;

/**
 * What a field type may need to know about the form being built.
 */
final class FieldTypeContext
{
    public function __construct(
        public readonly string $translationDomain,
        public readonly ?AdminContext $adminContext = null,
        /** The object the form is bound to, when any. */
        public readonly ?object $data = null,
    ) {
    }
}
