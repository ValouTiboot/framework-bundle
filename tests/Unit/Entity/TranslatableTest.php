<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Entity;

use Digitix\FrameworkBundle\Entity\Cms;
use Digitix\FrameworkBundle\Entity\CmsTranslation;
use Digitix\FrameworkBundle\Entity\Language;
use PHPUnit\Framework\TestCase;

final class TranslatableTest extends TestCase
{
    public function testMagicAccessorsFollowTheCurrentLanguage(): void
    {
        $fr = $this->language(1, 'fr_FR');
        $en = $this->language(2, 'en_GB');

        $cms = new Cms();
        $cms->addTranslation((new CmsTranslation())->setLanguage($fr)->setName('Bonjour'));
        $cms->addTranslation((new CmsTranslation())->setLanguage($en)->setName('Hello'));

        $cms->setCurrentLanguageId(1);
        self::assertSame('Bonjour', $cms->name);
        self::assertSame('Bonjour', $cms->getName());
        self::assertTrue(isset($cms->name));

        $cms->setCurrentLanguageId(2);
        self::assertSame('Hello', $cms->getName());

        self::assertSame([1 => 'Bonjour', 2 => 'Hello'], $cms->translatableName);

        $cms->translatableName = [1 => 'Salut', 2 => 'Hi', 99 => 'ignored'];
        self::assertSame([1 => 'Salut', 2 => 'Hi'], $cms->translatableName);

        // unknown language falls back to the first translation
        $cms->setCurrentLanguageId(42);
        self::assertSame('Salut', $cms->getName());
    }

    public function testUnknownMembers(): void
    {
        $cms = new Cms();

        $property = 'nope';
        self::assertNull($cms->$property);
        self::assertFalse(isset($cms->$property));

        $this->expectException(\BadMethodCallException::class);
        $cms->getNope(); // @phpstan-ignore method.notFound
    }

    private function language(int $id, string $locale): Language
    {
        $language = (new Language())->setLocale($locale)->setName($locale)->setIso(substr($locale, 0, 2));

        $property = new \ReflectionProperty(Language::class, 'id');
        $property->setValue($language, $id);

        return $language;
    }
}
