<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Config;

use Digitix\FrameworkBundle\Admin\Config\AdminConfigFactory;
use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Exception\UnknownEntityException;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Entity\Cms;
use PHPUnit\Framework\TestCase;

final class AdminConfigFactoryTest extends TestCase
{
    public function testBuildsTheObjectGraph(): void
    {
        $config = AdminConfigFactory::fromArray([
            'admin_menu' => ['cms' => ['title' => 'Pages']],
            'admin_entities' => [
                'Cms' => [
                    'class' => Cms::class,
                    'controller' => AdminController::class,
                    'view' => ['template' => null],
                    'list' => [
                        'has_create' => true,
                        'items_per_page' => 10,
                        'actions' => ['edit', 'delete', 'bogus'],
                        'fields' => [
                            'id' => ['label' => 'label.id', 'type' => 'text', 'sort' => true],
                            'name' => ['label' => 'label.name', 'sort' => true, 'default_sort' => 'DESC', 'column' => 'title'],
                        ],
                        'filters' => ['name' => ['type' => 'text', 'alias' => 't']],
                    ],
                    'form' => [
                        'fields' => [
                            'translatableName' => ['label' => 'label.name', 'name' => 'name', 'type' => 'translate', 'custom' => 42],
                        ],
                    ],
                ],
                'Dashboard' => ['controller' => AdminController::class],
            ],
        ]);

        self::assertSame(['cms' => ['title' => 'Pages']], $config->getMenu());
        self::assertTrue($config->hasEntity('cms'));
        self::assertTrue($config->hasEntity('CMS'), 'entity names are case-insensitive');

        $cms = $config->getEntity('cms');
        self::assertSame('Cms', $cms->name);
        self::assertSame('cms', $cms->getSlug());
        self::assertSame(Cms::class, $cms->class);
        self::assertTrue($cms->isTranslatable());
        self::assertFalse($cms->isVirtual());

        self::assertTrue($cms->list->hasCreate);
        self::assertSame(10, $cms->list->itemsPerPage);
        self::assertSame(['edit', 'delete', 'bogus'], $cms->list->actions, 'unknown actions are filtered at render time, not here');
        self::assertSame(['name', 'desc'], $cms->list->getDefaultSort());
        self::assertSame('title', $cms->list->getField('name')?->getColumn());
        self::assertSame('t', $cms->list->getFilter('name')?->alias);

        $field = $cms->form->getField('translatableName');
        self::assertInstanceOf(FieldConfig::class, $field);
        self::assertSame('name', $field->property);
        self::assertSame('translate', $field->type);
        self::assertSame(42, $field->get('custom'), 'unknown YAML keys are kept as options');
        self::assertSame('Admin.Fields.Label', $cms->form->translationDomain);

        $dashboard = $config->getEntity('dashboard');
        self::assertTrue($dashboard->isVirtual());
        self::assertSame(30, $dashboard->list->itemsPerPage, 'defaults apply when the list is not configured');
    }

    public function testUnknownEntityThrows(): void
    {
        $config = AdminConfigFactory::fromArray(['admin_entities' => ['Cms' => ['controller' => AdminController::class]]]);

        $this->expectException(UnknownEntityException::class);
        $this->expectExceptionMessage('"product"');

        $config->getEntity('product');
    }

    public function testFieldConfigDefaults(): void
    {
        $field = FieldConfig::fromArray('active', []);

        self::assertSame('active', $field->property);
        self::assertSame('text', $field->type);
        self::assertNull($field->label);
        self::assertFalse($field->isSortable());
        self::assertNull($field->isRequired());
        self::assertFalse($field->has('help'));
        self::assertSame('x', $field->get('help', 'x'));
    }
}
