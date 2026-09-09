<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin;

/**
 * Colour mode of the admin, stored as the "adminTheme" Configuration row.
 *
 * "system" leaves the choice to the browser: the layout resolves it with the
 * prefers-color-scheme media query, which reflects the operating system
 * setting (the server never knows it).
 */
final class AdminTheme
{
    public const CONFIGURATION_KEY = 'adminTheme';

    public const SYSTEM = 'system';
    public const LIGHT = 'light';
    public const DARK = 'dark';

    public const ALL = [self::SYSTEM, self::LIGHT, self::DARK];

    public static function isValid(?string $theme): bool
    {
        return \in_array($theme, self::ALL, true);
    }

    /** The stored value, or "system" when missing or unknown. */
    public static function normalize(?string $theme): string
    {
        return self::isValid($theme) ? $theme : self::SYSTEM;
    }
}
