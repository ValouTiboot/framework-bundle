<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

/**
 * Lifecycle of a translation entry.
 *
 *  missing:    the key exists in the code, no value has been entered yet
 *  translated: a value exists
 *  obsolete:   the key is no longer found in the code (value kept, in case
 *              the key comes back)
 */
enum TranslationStatus: string
{
    case Missing = 'missing';
    case Translated = 'translated';
    case Obsolete = 'obsolete';

    /** @return string[] */
    public static function values(): array
    {
        return array_map(static fn (self $status) => $status->value, self::cases());
    }
}
