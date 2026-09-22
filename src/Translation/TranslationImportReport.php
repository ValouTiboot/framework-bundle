<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

/**
 * What an import did.
 */
final class TranslationImportReport
{
    /** Entries created (keys unknown here). */
    public int $added = 0;

    /** Existing entries whose value changed. */
    public int $updated = 0;

    /** Existing entries already holding the imported value. */
    public int $unchanged = 0;

    /** Entries left alone: empty value, or existing translation kept because overwrite was off. */
    public int $skipped = 0;

    /** Malformed entries (no domain or key). */
    public int $invalid = 0;

    public function __construct(
        public readonly string $locale,
        public readonly bool $overwrite,
    ) {
    }

    public function total(): int
    {
        return $this->added + $this->updated + $this->unchanged + $this->skipped + $this->invalid;
    }

    /** @return array<string, int|string|bool> */
    public function toArray(): array
    {
        return [
            'locale' => $this->locale,
            'overwrite' => $this->overwrite,
            'added' => $this->added,
            'updated' => $this->updated,
            'unchanged' => $this->unchanged,
            'skipped' => $this->skipped,
            'invalid' => $this->invalid,
        ];
    }
}
