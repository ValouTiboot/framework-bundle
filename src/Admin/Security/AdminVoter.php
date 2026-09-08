<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Security;

use Digitix\FrameworkBundle\Admin\Config\EntityConfig;
use Digitix\FrameworkBundle\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Per-entity permissions.
 *
 * Super admins may do everything. Other users are granted what their Role
 * declares in its "authorization" JSON column:
 *
 *   { "*": ["read"], "cms": ["read", "create", "edit"], "user": ["*"] }
 *
 * Keys are lower-cased entity names ("*" applies to every entity), values are
 * permission short names ("*" grants all of them).
 *
 * @extends Voter<string, EntityConfig|string|null>
 */
final class AdminVoter extends Voter
{
    public const SUPER_ADMIN_ROLE = 'ROLE_SUPERADMIN';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, AdminPermission::ALL, true)
            && (null === $subject || \is_string($subject) || $subject instanceof EntityConfig);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(self::SUPER_ADMIN_ROLE)) {
            return true;
        }

        $authorization = $user->getRole()?->getAuthorization() ?? [];
        $entity = strtolower($subject instanceof EntityConfig ? $subject->name : (string) $subject);
        $permission = AdminPermission::shortName($attribute);

        $granted = array_merge(
            (array) ($authorization['*'] ?? []),
            '' !== $entity ? (array) ($authorization[$entity] ?? []) : [],
        );

        return \in_array('*', $granted, true) || \in_array($permission, $granted, true);
    }
}
