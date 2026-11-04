<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Utils;

use Digitix\FrameworkBundle\Utils\SpamGuard;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

final class SpamGuardTest extends TestCase
{
    private SpamGuard $guard;

    protected function setUp(): void
    {
        $this->guard = new SpamGuard('secret');
    }

    public function testTheGuardFieldsAreUnmappedAndTheHoneypotIsHiddenFromHumans(): void
    {
        $definitions = $this->guard->definitions(1_000);

        self::assertSame(['website', 'renderedAt'], array_column($definitions, 'name'));
        self::assertFalse($definitions[0]['options']['mapped']);
        self::assertFalse($definitions[0]['options']['required']);
        self::assertSame('-1', $definitions[0]['options']['attr']['tabindex']);
        self::assertSame('true', $definitions[0]['options']['row_attr']['aria-hidden']);
        self::assertStringContainsString('left:-9999px', $definitions[0]['options']['row_attr']['style']);
        self::assertFalse($definitions[1]['options']['mapped']);
        self::assertSame($this->guard->sign(1_000), $definitions[1]['options']['data']);
    }

    public function testAHumanSubmissionIsNotSpam(): void
    {
        $form = $this->submit(['website' => '', 'renderedAt' => $this->guard->sign(1_000)]);

        self::assertFalse($this->guard->isSpam($form, 1_000 + SpamGuard::MIN_SECONDS));
        self::assertSame(['name' => 'Jane'], $form->getData(), 'guard fields never reach the data');
    }

    public function testAFilledHoneypotIsSpam(): void
    {
        $form = $this->submit(['website' => 'http://spam.test', 'renderedAt' => $this->guard->sign(1_000)]);

        self::assertTrue($this->guard->isSpam($form, 2_000));
    }

    public function testATooFastSubmissionIsSpam(): void
    {
        $form = $this->submit(['website' => '', 'renderedAt' => $this->guard->sign(1_000)]);

        self::assertTrue($this->guard->isSpam($form, 1_000 + SpamGuard::MIN_SECONDS - 1));
    }

    public function testAForgedOrMissingTimestampIsSpam(): void
    {
        self::assertTrue($this->guard->isSpam($this->submit(['website' => '', 'renderedAt' => '1000.deadbeef']), 2_000));
        self::assertTrue($this->guard->isSpam($this->submit(['website' => '', 'renderedAt' => '']), 2_000));
        self::assertTrue($this->guard->isSpam($this->submit(['website' => '']), 2_000));

        $otherSecret = new SpamGuard('other');
        self::assertTrue($this->guard->isSpam($this->submit(['website' => '', 'renderedAt' => $otherSecret->sign(1_000)]), 2_000));
    }

    public function testAFormWithoutTheGuardFieldsIsNeverSpam(): void
    {
        $form = Forms::createFormFactory()->createNamedBuilder('plain')->add('name', TextType::class)->getForm();
        $form->submit(['name' => 'Jane']);

        self::assertFalse($this->guard->isSpam($form, 2_000));
    }

    /**
     * @param array<string, string> $guardValues
     *
     * @return FormInterface<mixed>
     */
    private function submit(array $guardValues): FormInterface
    {
        $builder = Forms::createFormFactory()->createNamedBuilder('contact')->add('name', TextType::class);

        foreach ($this->guard->definitions(1_000) as $definition) {
            $builder->add($definition['name'], $definition['type'], $definition['options']);
        }

        $form = $builder->getForm();
        $form->submit(array_merge(['name' => 'Jane'], $guardValues));

        return $form;
    }
}
