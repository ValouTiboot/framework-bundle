<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Exception;

final class UnknownTypeException extends \InvalidArgumentException
{
    /**
     * @param string[] $known
     */
    public function __construct(string $kind, string $type, array $known = [])
    {
        parent::__construct(sprintf(
            'Unknown %s type "%s". Known types: %s.',
            $kind,
            $type,
            $known ? implode(', ', $known) : '(none registered)'
        ));
    }
}
