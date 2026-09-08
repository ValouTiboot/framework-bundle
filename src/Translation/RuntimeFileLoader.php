<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Loader of the generated catalogue files. Unlike the standard loaders it
 * tolerates a file that disappeared (a domain emptied since the file was
 * registered): the catalogue is simply empty.
 */
final class RuntimeFileLoader extends PhpFileLoader
{
    public const FORMAT = 'dgtx_runtime';

    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        if (!\is_string($resource) || !is_file($resource)) {
            return new MessageCatalogue($locale);
        }

        return parent::load($resource, $locale, $domain);
    }
}
