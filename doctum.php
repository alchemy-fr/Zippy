<?php

/*
 * Configuration for Doctum (https://doctum.long-term.support), the maintained
 * fork of the abandoned Sami API documentation generator.
 *
 * Regenerate the API documentation with:
 *
 *     curl -O https://doctum.long-term.support/releases/latest/doctum.phar
 *     php doctum.phar update doctum.php
 *
 * The output lands in `build/api` (git-ignored) and is NOT committed to the
 * repository. Publish it from your documentation pipeline instead.
 */

use Doctum\Doctum;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/src');

return new Doctum($iterator, array(
    'title'     => 'Zippy API',
    'build_dir' => __DIR__ . '/build/api',
    'cache_dir' => __DIR__ . '/build/cache/api',
));
