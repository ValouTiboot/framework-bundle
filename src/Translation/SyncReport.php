<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

/**
 * What a synchronisation did, all locales together.
 */
final class SyncReport
{
    /** Distinct keys found in the code (all domains). */
    public int $keys = 0;

    /** @var string[] */
    public array $locales = [];

    /** Entries created (one per key and locale). */
    public int $added = 0;

    /** Created entries that received a value from an existing translation file. */
    public int $seeded = 0;

    /** Obsolete entries whose key is back in the code. */
    public int $reactivated = 0;

    /** Entries whose key disappeared from the code. */
    public int $obsoleted = 0;

    /** @return array<string, int|string> */
    public function toArray(): array
    {
        return [
            'keys' => $this->keys,
            'locales' => implode(', ', $this->locales),
            'added' => $this->added,
            'seeded' => $this->seeded,
            'reactivated' => $this->reactivated,
            'obsoleted' => $this->obsoleted,
        ];
    }
}
