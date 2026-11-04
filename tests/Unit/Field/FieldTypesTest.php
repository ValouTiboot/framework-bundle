<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Field;

use Digitix\FrameworkBundle\Admin\Config\FieldConfig;
use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeContext;
use Digitix\FrameworkBundle\Admin\Field\FieldTypeRegistry;
use Digitix\FrameworkBundle\Admin\Field\Type\BoolFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\ChoiceFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\EmailFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\EmbedFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\SubmitFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\TextareaFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\TextFieldType;
use Digitix\FrameworkBundle\Admin\Field\Type\TranslateFieldType;
use Digitix\FrameworkBundle\Admin\Exception\UnknownTypeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class FieldTypesTest extends TestCase
{
    /** A front form: no admin context. */
    private FieldTypeContext $context;

    protected function setUp(): void
    {
        $this->context = new FieldTypeContext('Admin.Fields.Label');
    }

    public function testFrontFormsGetImplicitConstraints(): void
    {
        self::assertTrue($this->context->isFrontForm());

        $text = (new TextFieldType())->getFormOptions(FieldConfig::fromArray('lastname', []), $this->context)['constraints'];
        self::assertSame([NotBlank::class, Length::class], array_map('get_class', $text), 'required by default');
        self::assertSame(TextFieldType::MAX_LENGTH, $text[1]->max);

        $optional = (new TextFieldType())->getFormOptions(FieldConfig::fromArray('company', ['required' => false]), $this->context)['constraints'];
        self::assertSame([Length::class], array_map('get_class', $optional));

        $email = (new EmailFieldType())->getFormOptions(FieldConfig::fromArray('mail', []), $this->context)['constraints'];
        self::assertSame([NotBlank::class, Email::class, Length::class], array_map('get_class', $email));

        $message = (new TextareaFieldType())->getFormOptions(FieldConfig::fromArray('message', []), $this->context)['constraints'];
        self::assertSame(TextareaFieldType::MAX_LENGTH, $message[1]->max);

        $switch = (new BoolFieldType())->getFormOptions(FieldConfig::fromArray('active', ['required' => true]), $this->context);
        self::assertArrayNotHasKey('constraints', $switch, '"no" is a valid answer');

        $submit = (new SubmitFieldType())->getFormOptions(FieldConfig::fromArray('send', ['constraints' => ['NotBlank']]), $this->context);
        self::assertArrayNotHasKey('constraints', $submit, 'SubmitType has no such option');
    }

    public function testDeclaredConstraintsReplaceTheImplicitOnes(): void
    {
        $options = (new TextareaFieldType())->getFormOptions(
            FieldConfig::fromArray('message', ['constraints' => [['Length' => ['min' => 20, 'max' => 2000]]]]),
            $this->context
        );

        self::assertSame([NotBlank::class, Length::class], array_map('get_class', $options['constraints']));
        self::assertSame(20, $options['constraints'][1]->min);
        self::assertSame(2000, $options['constraints'][1]->max);
    }

    public function testAdminFormsOnlyGetTheDeclaredConstraints(): void
    {
        // any admin context will do: only its presence matters here
        $adminContext = (new \ReflectionClass(AdminContext::class))->newInstanceWithoutConstructor();
        $admin = new FieldTypeContext('Admin.Fields.Label', $adminContext);
        self::assertFalse($admin->isFrontForm());

        $plain = (new TextFieldType())->getFormOptions(FieldConfig::fromArray('name', ['required' => true]), $admin);
        self::assertArrayNotHasKey('constraints', $plain);

        $declared = (new TextFieldType())->getFormOptions(FieldConfig::fromArray('name', ['constraints' => ['NotBlank']]), $admin);
        self::assertSame([NotBlank::class], array_map('get_class', $declared['constraints']));
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

    public function testEmbedCollection(): void
    {
        $type = new EmbedFieldType();
        $options = $type->getFormOptions(FieldConfig::fromArray('items', ['entryType' => TextType::class, 'help' => 'help.items']), $this->context);

        self::assertSame(CollectionType::class, $type->getFormType(FieldConfig::fromArray('items', []), $this->context));
        self::assertSame(TextType::class, $options['entry_type']);
        self::assertTrue($options['allow_add']);
        self::assertTrue($options['allow_delete']);
        self::assertFalse($options['by_reference'], 'the parent adder and remover keep the relation in sync');
        self::assertSame('dgtx_embed', $options['block_prefix']);
        self::assertSame('help.items', $options['help']);

        $this->expectException(\InvalidArgumentException::class);
        $type->getFormOptions(FieldConfig::fromArray('items', ['entryType' => 'App\\Nope']), $this->context);
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
