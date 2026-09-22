<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Security;

/**
 * Security attributes understood by AdminVoter. The subject is an
 * EntityConfig (or an entity name).
 */
final class AdminPermission
{
    public const VIEW = 'DGTX_ADMIN_VIEW';
    public const READ = 'DGTX_ADMIN_READ';
    public const CREATE = 'DGTX_ADMIN_CREATE';
    public const EDIT = 'DGTX_ADMIN_EDIT';
    public const DELETE = 'DGTX_ADMIN_DELETE';

    public const ALL = [self::VIEW, self::READ, self::CREATE, self::EDIT, self::DELETE];

    /** Short name as stored in Role::authorization ("read", "edit"...). */
    public static function shortName(string $attribute): string
    {
        return strtolower(substr($attribute, \strlen('DGTX_ADMIN_')));
    }

    /** Maps a list "action" (edit, delete, view, view_entity) to its permission. */
    public static function forAction(string $action): string
    {
        return match ($action) {
            'view', 'view_entity' => self::VIEW,
            'create', 'add' => self::CREATE,
            'edit' => self::EDIT,
            'delete' => self::DELETE,
            default => self::READ,
        };
    }

    private function __construct()
    {
    }
}
