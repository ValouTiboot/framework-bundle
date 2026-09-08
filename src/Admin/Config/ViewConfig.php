<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Admin\Config;

final class ViewConfig
{
    public function __construct(
        public readonly ?string $template,
    ) {
    }
}
