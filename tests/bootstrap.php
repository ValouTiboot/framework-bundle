<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

umask(0000);

$var = __DIR__.'/App/var';
if (!is_dir($var)) {
    mkdir($var, 0777, true);
}
