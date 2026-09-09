<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

/**
 * What a menu item points to.
 */
enum MenuItemType: string
{
    /** A route of the project (static page), with optional parameters. */
    case Route = 'route';

    /** A CMS page (front_cms_show + the page id). */
    case Cms = 'cms';

    /** A free URL, internal or external. */
    case Link = 'link';

    public const CMS_ROUTE = 'front_cms_show';

    /** @return string[] */
    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }
}
