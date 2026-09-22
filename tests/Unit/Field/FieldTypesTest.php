<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Field;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeRegistry;
use Digitix\FrameworkBundle\Admin\Field\Type\BoolFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\ChoiceFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\TextareaFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\TextFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\TranslateFieldType;
use Digitix\FrameworkBundle\Admin\Exception\UnknownTypeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class FieldTypesTest extends TestCase
{
    private FieldTypeContext $context;

    protected function setUp(): void
    {
        $this->context = new FieldTypeContext('Admin.Fields.Label');
    }

    public function testCommonOptionsAndCssClass(): void
    {
        $field = FieldConfig::fromArray('name', [
            'label' => 'label.name',
            'required' => false,
            'help' => 'help.name',
            'class' => 'tinymce',
            'attr' => ['placeholder' => 'x', 'class' => 'big'],
        ]);

        $options = (new TextFieldType())->getFormOptions($field, $this->context);

        self::assertSame('label.name', $options['label']);
        self::assertSame('Admin.Fields.Label', $options['translation_domain']);
        self::assertFalse($options['required']);
        self::assertSame('help.name', $options['help']);
        self::assertSame(['placeholder' => 'x', 'class' => 'big tinymce'], $options['attr']);
    }

    public function testTextareaRows(): void
    {
        $field = FieldConfig::fromArray('content', ['rows' => 12, 'class' => 'tinymce']);
        $type = new TextareaFieldType();

        self::assertSame(TextareaType::class, $type->getFormType($field, $this->context));
        self::assertSame(['class' => 'tinymce', 'rows' => 12], $type->getFormOptions($field, $this->context)['attr']);
    }

    public function testBoolSwitch(): void
    {
        $options = (new BoolFieldType())->getFormOptions(FieldConfig::fromArray('active', ['required' => true]), $this->context);

        self::assertSame(['yes' => true, 'no' => false], $options['choices']);
        self::assertTrue($options['expanded']);
        self::assertSame('dgtx-switch', $options['attr']['class']);
        self::assertArrayNotHasKey('placeholder', $options, 'required switches have no empty choice');
        self::assertSame('label.default.yes', $options['choice_label'](true, 'yes'));

        $optional = (new BoolFieldType())->getFormOptions(FieldConfig::fromArray('active', []), $this->context);
        self::assertSame('', $optional['placeholder']);
    }

    public function testChoiceFromMapOrCallback(): void
    {
        $type = new ChoiceFieldType();
        self::assertSame(ChoiceType::class, $type->getFormType(FieldConfig::fromArray('x', []), $this->context));

        $map = $type->getFormOptions(FieldConfig::fromArray('type', ['choice' => ['label.a' => 'a']]), $this->context);
        self::assertSame(['label.a' => 'a'], $map['choices']);

        $callback = $type->getFormOptions(FieldConfig::fromArray('type', ['callback' => self::class.'::choices']), $this->context);
        self::assertSame(['from' => 'callback'], $callback['choices']);

        $this->expectException(\InvalidArgumentException::class);
        $type->getFormOptions(FieldConfig::fromArray('type', ['callback' => 'not a callable']), $this->context);
    }

    public function testTranslateCollection(): void
    {
        $options = (new TranslateFieldType())->getFormOptions(
            FieldConfig::fromArray('translatableName', ['name' => 'name', 'class' => 'tag', 'required' => true]),
            $this->context
        );

        self::assertSame(TextType::class, $options['entry_type']);
        self::assertSame(['attr' => ['class' => 'tag'], 'required' => true, 'label' => false], $options['entry_options']);
        self::assertArrayNotHasKey('attr', $options, 'attributes go to the entries, not the wrapper');
        self::assertFalse($options['allow_add']);
    }

    public function testRegistry(): void
    {
        $registry = new FieldTypeRegistry(['text' => new TextFieldType(), new BoolFieldType()]);

        self::assertTrue($registry->has('text'));
        self::assertTrue($registry->has('bool'), 'unnamed types are indexed by getTypeName()');
        self::assertSame(['text', 'bool'], $registry->getNames());

        $this->expectException(UnknownTypeException::class);
        $registry->get('nope');
    }

    /** @return array<string, string> */
    public static function choices(): array
    {
        return ['from' => 'callback'];
    }
}
