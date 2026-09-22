<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Entity;

use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Translation\TranslationStatus;
use PHPUnit\Framework\TestCase;

final class TranslationTest extends TestCase
{
    public function testANewEntryIsMissing(): void
    {
        $translation = Translation::create('Admin.List.Default', 'list.default.add', 'fr_FR');

        self::assertSame(TranslationStatus::Missing, $translation->getStatus());
        self::assertSame('missing', $translation->getStatusValue());
        self::assertFalse($translation->isTranslated());
        self::assertSame(md5('list.default.add'), $translation->getKeyHash());
    }

    public function testAValueMakesItTranslatedAndABlankValueMissingAgain(): void
    {
        $translation = Translation::create('Admin.List.Default', 'list.default.add', 'fr_FR');

        $translation->setValue('  Ajouter  ');
        self::assertSame('Ajouter', $translation->getValue());
        self::assertSame(TranslationStatus::Translated, $translation->getStatus());
        self::assertNotNull($translation->getDateUpd());

        $translation->setValue('   ');
        self::assertNull($translation->getValue());
        self::assertSame(TranslationStatus::Missing, $translation->getStatus());
    }

    public function testObsoleteKeepsItsValueUntilTheKeyIsBack(): void
    {
        $translation = Translation::create('Admin.List.Default', 'list.default.add', 'fr_FR')->setValue('Ajouter');

        $translation->markObsolete();
        self::assertTrue($translation->isObsolete());
        self::assertSame('Ajouter', $translation->getValue());

        // editing an obsolete entry does not resurrect it
        $translation->setValue('Créer');
        self::assertTrue($translation->isObsolete());

        $translation->markActive();
        self::assertSame(TranslationStatus::Translated, $translation->getStatus());

        $translation->setValue(null)->markObsolete()->markActive();
        self::assertSame(TranslationStatus::Missing, $translation->getStatus());
    }
}
