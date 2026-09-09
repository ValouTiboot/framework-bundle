<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Menu;

use Digitix\FrameworkBundle\Menu\MenuItemType;
use Digitix\FrameworkBundle\Menu\MenuNode;
use Digitix\FrameworkBundle\Menu\MenuTree;
use PHPUnit\Framework\TestCase;

final class MenuTreeTest extends TestCase
{
    public function testMarkCurrentFlagsTheNodeAndItsAncestors(): void
    {
        $tree = $this->tree();
        $tree->markCurrent('/about/team/');

        [$home, $about] = $tree->items;
        self::assertFalse($home->current);
        self::assertFalse($home->active);
        self::assertFalse($about->current);
        self::assertTrue($about->active, 'ancestor of the current node');
        self::assertTrue($about->children[0]->current);
        self::assertFalse($about->children[0]->active);
    }

    public function testMarkCurrentIgnoresQueryStringsAndExternalLinks(): void
    {
        $tree = $this->tree();
        $tree->markCurrent('/');

        self::assertTrue($tree->items[0]->current, 'the query string of the node URL is ignored');
        self::assertFalse($tree->items[2]->current, 'an external link never matches');
    }

    public function testArrayRoundTrip(): void
    {
        $tree = $this->tree();
        $copy = MenuTree::fromArray(json_decode((string) json_encode($tree->toArray()), true));

        self::assertSame($tree->toArray(), $copy->toArray());
        self::assertSame('main', $copy->code);
        self::assertSame(MenuItemType::Link, $copy->items[2]->type);
        self::assertTrue($copy->items[1]->hasChildren());
        self::assertFalse($copy->isEmpty());
    }

    private function tree(): MenuTree
    {
        return new MenuTree(1, 'main', 'Main menu', 'fr_FR', [
            new MenuNode(1, 'Home', '/?utm=x', null, null, MenuItemType::Route, 0),
            new MenuNode(2, 'About', '/about', null, 'about', MenuItemType::Cms, 0, [
                new MenuNode(3, 'Team', '/about/team', null, null, MenuItemType::Cms, 1),
            ]),
            new MenuNode(4, 'Digitix', 'https://digitix.agency/', '_blank', null, MenuItemType::Link, 0),
        ]);
    }
}
