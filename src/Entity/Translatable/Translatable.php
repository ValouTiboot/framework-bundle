<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Entity\Translatable;

use Doctrine\Common\Collections\ReadableCollection;

/**
 * Base class for entities whose text fields live in a sibling
 * "<Entity>Translation" entity (one row per language).
 *
 * Magic accessors, kept for compatibility with the YAML forms and templates:
 *
 *   $cms->name / $cms->getName()   value in the current language
 *   $cms->translatableName         [languageId => value] for every translation
 *   $cms->translatableName = [...] sets each translation from a [languageId => value] map
 *
 * The current language is injected by TranslatableLanguageListener on load and
 * by EntityInstantiator on creation; it is never persisted.
 */
abstract class Translatable
{
    private const PREFIX = 'translatable';

    private int $currentLanguageId = 1;

    /** @return ReadableCollection<int, object> */
    abstract public function getTranslations(): ReadableCollection;

    public function setCurrentLanguageId(int $languageId): void
    {
        $this->currentLanguageId = $languageId;
    }

    public function getCurrentLanguageId(): int
    {
        return $this->currentLanguageId;
    }

    /**
     * Translation for the given language (current one by default), falling
     * back to the first available translation.
     */
    public function getTranslation(?int $languageId = null): ?object
    {
        $languageId ??= $this->currentLanguageId;

        foreach ($this->getTranslations() as $translation) {
            if ($translation->getLanguage()?->getId() === $languageId) {
                return $translation;
            }
        }

        $first = $this->getTranslations()->first();

        return false === $first ? null : $first;
    }

    public function __get(string $name): mixed
    {
        if (null !== ($getter = $this->translatableGetter($name))) {
            $values = [];
            foreach ($this->getTranslations() as $translation) {
                $values[$translation->getLanguage()?->getId()] = $translation->$getter();
            }

            return $values;
        }

        $getter = 'get'.ucfirst($name);
        $translation = $this->getTranslation();

        return null !== $translation && method_exists($translation, $getter) ? $translation->$getter() : null;
    }

    public function __set(string $name, mixed $value): void
    {
        if (null === ($setter = $this->translatableSetter($name))) {
            throw new \LogicException(sprintf('Cannot set undefined property "%s" on %s.', $name, static::class));
        }

        if (!\is_array($value)) {
            return;
        }

        foreach ($this->getTranslations() as $translation) {
            $languageId = $translation->getLanguage()?->getId();

            if (null !== $languageId && \array_key_exists($languageId, $value)) {
                $translation->$setter($value[$languageId]);
            }
        }
    }

    public function __isset(string $name): bool
    {
        if (null !== $this->translatableGetter($name)) {
            return true;
        }

        $translation = $this->getTranslation();

        return null !== $translation && method_exists($translation, 'get'.ucfirst($name));
    }

    /**
     * @param array<int, mixed> $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        $getter = str_starts_with($method, 'get') ? $method : 'get'.ucfirst($method);
        $translation = $this->getTranslation();

        if (null !== $translation && method_exists($translation, $getter)) {
            return $translation->$getter(...$arguments);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s().', static::class, $method));
    }

    private function translatableGetter(string $name): ?string
    {
        if (!str_starts_with($name, self::PREFIX)) {
            return null;
        }

        $getter = 'get'.substr($name, \strlen(self::PREFIX));

        return method_exists(static::class.'Translation', $getter) ? $getter : null;
    }

    private function translatableSetter(string $name): ?string
    {
        if (!str_starts_with($name, self::PREFIX)) {
            return null;
        }

        $setter = 'set'.substr($name, \strlen(self::PREFIX));

        return method_exists(static::class.'Translation', $setter) ? $setter : null;
    }
}
