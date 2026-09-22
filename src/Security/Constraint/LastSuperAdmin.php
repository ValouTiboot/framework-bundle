<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Security\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * A Role cannot lose its super admin flag when it is the last super admin
 * role: nobody could administer the site any more.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class LastSuperAdmin extends Constraint
{
    public string $message = 'This is the last super admin role, it must keep full access.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
