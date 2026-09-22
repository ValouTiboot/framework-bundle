<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Exception;

final class UnknownEntityException extends \InvalidArgumentException
{
    /**
     * @param string[] $known
     */
    public function __construct(string $name, array $known = [])
    {
        $message = sprintf('No admin entity named "%s" is configured under "digitix_framework".', $name);

        if ($known) {
            $message .= sprintf(' Known entities: %s.', implode(', ', $known));
        }

        parent::__construct($message);
    }
}
