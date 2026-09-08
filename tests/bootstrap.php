<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

umask(0000);

$var = __DIR__.'/App/var';
if (!is_dir($var)) {
    mkdir($var, 0777, true);
}

// With APP_DEBUG=0 the compiled container is never checked for freshness:
// start every run from a clean cache so code and configuration changes apply.
(new Symfony\Component\Filesystem\Filesystem())->remove([$var.'/cache', $var.'/translations']);
