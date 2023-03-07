<?php

use Symfony\Component\Filesystem\Filesystem;

$vendorDir = dirname(__DIR__).'/vendor';
$pharPath = "{$vendorDir}/phpstan/phpstan/phpstan.phar";
$extractPath = "{$vendorDir}/phpstan/phpstan-src";

if (is_file($pharPath)) {
    require "{$vendorDir}/autoload.php";

    (new Filesystem())
        ->remove($extractPath);

    $phar = new Phar($pharPath);
    $phar->extractTo($extractPath);
}
