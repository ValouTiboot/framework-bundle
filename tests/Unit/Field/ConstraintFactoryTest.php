<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Field;

use Digitix\FrameworkBundle\Admin\Field\ConstraintFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class ConstraintFactoryTest extends TestCase
{
    public function testNamesMapsAndClassNames(): void
    {
        $constraints = ConstraintFactory::fromDefinitions('message', [
            'NotBlank',
            ['Length' => ['min' => 10, 'max' => 2000]],
            ['Regex' => ['pattern' => '/^[a-z]+$/']],
            Email::class,
        ]);

        self::assertCount(4, $constraints);
        self::assertInstanceOf(NotBlank::class, $constraints[0]);
        self::assertInstanceOf(Length::class, $constraints[1]);
        self::assertSame(10, $constraints[1]->min);
        self::assertSame(2000, $constraints[1]->max);
        self::assertInstanceOf(Regex::class, $constraints[2]);
        self::assertSame('/^[a-z]+$/', $constraints[2]->pattern);
        self::assertInstanceOf(Email::class, $constraints[3]);
    }

    public function testNothingDeclaredGivesNothing(): void
    {
        self::assertSame([], ConstraintFactory::fromDefinitions('x', null));
        self::assertSame([], ConstraintFactory::fromDefinitions('x', []));
    }

    public function testUnknownConstraint(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('unknown constraint "Nope"');

        ConstraintFactory::fromDefinitions('x', ['Nope']);
    }

    public function testAClassThatIsNotAConstraintIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConstraintFactory::fromDefinitions('x', [self::class]);
    }

    public function testPositionalOptionsAreRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be named');

        ConstraintFactory::fromDefinitions('x', [['Length' => [80]]]);
    }

    public function testMalformedDefinitions(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConstraintFactory::fromDefinitions('x', [['NotBlank' => null, 'Length' => ['max' => 1]]]);
    }

    public function testMergeDropsTheImplicitConstraintsDeclaredExplicitly(): void
    {
        $merged = ConstraintFactory::merge(
            [new NotBlank(), new Length(max: 255)],
            [new Length(max: 80)],
        );

        self::assertCount(2, $merged);
        self::assertInstanceOf(NotBlank::class, $merged[0]);
        self::assertInstanceOf(Length::class, $merged[1]);
        self::assertSame(80, $merged[1]->max, 'the declared Length wins over the implicit one');
    }
}
