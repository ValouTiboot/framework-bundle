<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Config\ListConfig;
use Digitix\FrameworkBundle\Admin\List\Paginator;
use Digitix\FrameworkBundle\Admin\List\Sorter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class ListTest extends TestCase
{
    public function testSorterDefaultsAndValidation(): void
    {
        $list = $this->listConfig();

        // default sort from configuration
        $sorter = Sorter::fromRequest(Request::create('/admin/cms'), $list, $this->urlGenerator(), 'cms');
        self::assertSame('id', $sorter->getOrderBy());
        self::assertSame('desc', $sorter->getOrderWay());

        // requested sort on a sortable field
        $sorter = Sorter::fromRequest(Request::create('/admin/cms', 'GET', ['sortBy' => 'title', 'sortWay' => 'DESC']), $list, $this->urlGenerator(), 'cms');
        self::assertSame('title', $sorter->getOrderBy());
        self::assertSame('name', $sorter->getProperty(), 'the entity property may differ from the field name');
        self::assertSame('desc', $sorter->getOrderWay());

        // unknown field, unknown direction: ignored
        $sorter = Sorter::fromRequest(Request::create('/admin/cms', 'GET', ['sortBy' => 'password', 'sortWay' => 'sideways']), $list, $this->urlGenerator(), 'cms');
        self::assertSame('id', $sorter->getOrderBy());
        self::assertSame('desc', $sorter->getOrderWay());

        // non sortable field: ignored
        $sorter = Sorter::fromRequest(Request::create('/admin/cms', 'GET', ['sortBy' => 'active']), $list, $this->urlGenerator(), 'cms');
        self::assertSame('id', $sorter->getOrderBy());

        self::assertStringContainsString('sortBy=title', $sorter->generateLink('title', 'asc'));
        self::assertStringContainsString('sortWay=asc', $sorter->generateLink('title', 'nonsense'));
    }

    public function testPaginatorPageFromRequest(): void
    {
        self::assertSame(1, Paginator::pageFromRequest(Request::create('/')));
        self::assertSame(1, Paginator::pageFromRequest(Request::create('/', 'GET', ['page' => '-4'])));
        self::assertSame(1, Paginator::pageFromRequest(Request::create('/', 'GET', ['page' => 'abc'])));
        self::assertSame(7, Paginator::pageFromRequest(Request::create('/', 'GET', ['page' => '7'])));
    }

    private function listConfig(): ListConfig
    {
        return new ListConfig(null, true, false, 30, [], [], [], [
            'id' => FieldConfig::fromArray('id', ['sort' => true, 'default_sort' => 'desc']),
            'title' => FieldConfig::fromArray('title', ['name' => 'name', 'sort' => true]),
            'active' => FieldConfig::fromArray('active', ['type' => 'bool']),
        ], []);
    }

    private function urlGenerator(): UrlGeneratorInterface
    {
        return new class() implements UrlGeneratorInterface {
            /** @param array<string, mixed> $parameters */
            public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
            {
                return '/'.$name.'?'.http_build_query($parameters);
            }

            public function setContext(RequestContext $context): void
            {
            }

            public function getContext(): RequestContext
            {
                return new RequestContext();
            }
        };
    }
}
