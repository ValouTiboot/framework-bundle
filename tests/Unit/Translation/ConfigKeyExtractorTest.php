<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Translation;

use Digitix\FrameworkBundle\Admin\Config\AdminConfigFactory;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;
use Digitix\FrameworkBundle\Translation\ConfigKeyExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\MessageCatalogue;

final class ConfigKeyExtractorTest extends TestCase
{
    private MessageCatalogue $catalogue;

    protected function setUp(): void
    {
        $config = AdminConfigFactory::fromArray([
            'admin_menu' => [],
            'admin_entities' => [
                'Product' => [
                    'class' => null,
                    'controller' => AdminController::class,
                    'list' => [
                        'header_link' => [['entity' => 'category', 'name' => 'label.product.addCategory']],
                        'fields' => [
                            'name' => ['label' => 'label.product.name', 'type' => 'text'],
                            'kind' => ['label' => 'label.product.kind', 'type' => 'choice'],
                        ],
                        'filters' => [
                            'name' => ['label' => 'label.product.name', 'type' => 'text'],
                            'active' => ['label' => 'label.default.active', 'type' => 'bool'],
                            'kind' => ['label' => 'label.product.kind', 'type' => 'choice', 'choice' => ['label.product.kind.book' => 'book']],
                        ],
                    ],
                    'form' => [
                        'translation_domain' => 'Admin.Product',
                        'has_auto_submit_button' => true,
                        'fields' => [
                            'name' => ['label' => 'label.product.name', 'type' => 'text', 'help' => 'help.product.name', 'attr' => ['placeholder' => 'placeholder.product.name']],
                            'kind' => ['label' => 'label.product.kind', 'type' => 'choice', 'choice' => ['label.product.kind.book' => 'book', 'label.product.kind.dvd' => 'dvd']],
                            'active' => ['label' => 'label.default.active', 'type' => 'bool'],
                        ],
                    ],
                ],
            ],
            'front_entities' => [
                'Contact' => [
                    'form' => [
                        'translation_domain' => 'Front.Contact',
                        'has_auto_submit_button' => false,
                        'fields' => [
                            'email' => ['label' => 'label.contact.email', 'type' => 'email'],
                            'send' => ['type' => 'submit'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->catalogue = new MessageCatalogue('en');
        (new ConfigKeyExtractor($config))->extract($this->catalogue);
    }

    public function testListLabelsAndHeaderLinksUseTheListDomain(): void
    {
        self::assertTrue($this->catalogue->defines('label.product.name', 'Admin.Fields.Label'));
        self::assertTrue($this->catalogue->defines('label.product.kind', 'Admin.Fields.Label'));
        self::assertTrue($this->catalogue->defines('label.product.addCategory', 'Admin.Fields.Label'));
    }

    public function testChoiceColumnsDeclareOneLabelPerValue(): void
    {
        self::assertTrue($this->catalogue->defines('label.product.book', 'Admin.Fields.Label'));
        self::assertTrue($this->catalogue->defines('label.product.dvd', 'Admin.Fields.Label'));
    }

    public function testFiltersDeclareTheirLabelsChoicesAndBooleans(): void
    {
        self::assertTrue($this->catalogue->defines('label.default.active', 'Admin.Fields.Label'));
        self::assertTrue($this->catalogue->defines('label.product.kind.book', 'Admin.Fields.Label'));
        self::assertTrue($this->catalogue->defines('label.default.yes', 'Admin.Fields.Label'));
        self::assertTrue($this->catalogue->defines('label.default.no', 'Admin.Fields.Label'));
    }

    public function testFormKeysUseTheFormDomain(): void
    {
        self::assertTrue($this->catalogue->defines('label.product.name', 'Admin.Product'));
        self::assertTrue($this->catalogue->defines('help.product.name', 'Admin.Product'));
        self::assertTrue($this->catalogue->defines('placeholder.product.name', 'Admin.Product'));
        self::assertTrue($this->catalogue->defines('label.product.kind.dvd', 'Admin.Product'));
        self::assertTrue($this->catalogue->defines('label.default.yes', 'Admin.Product'));
        self::assertTrue($this->catalogue->defines('form.default.submit', 'Admin.Form.Default'));
    }

    public function testFrontFormsAreExtractedToo(): void
    {
        self::assertTrue($this->catalogue->defines('label.contact.email', 'Front.Contact'));
        // a submit without label falls back to the default label, in the form domain
        self::assertTrue($this->catalogue->defines('form.default.submit', 'Front.Contact'));
    }

    public function testValuesAreEmpty(): void
    {
        self::assertSame('', $this->catalogue->get('label.product.name', 'Admin.Fields.Label'));
    }
}
