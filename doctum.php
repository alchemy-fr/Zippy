<?php

/*
 * Configuration for Doctum (https://doctum.long-term.support), the maintained
 * fork of the abandoned Sami API documentation generator.
 *
 * Regenerate the API documentation with:
 *
 *     make apidoc
 *
 * `make apidoc` fetches a pinned Doctum release and verifies its checksum before
 * running (see DOCTUM_VERSION / DOCTUM_SHA256 in the Makefile).
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
