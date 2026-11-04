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

    /**
     * A public form (front_entities), built outside any admin request. Such
     * forms get implicit validation constraints, admin forms do not.
     */
    public function isFrontForm(): bool
    {
        return null === $this->adminContext;
    }
}
