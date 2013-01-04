<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\ArchiveInterface;
use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Options;

Interface AdapterInterface
{
    /**
     * Returns the adapter options
     *
     * @return Options
     */
    public function getOptions();

    /**
     * Sets adapter options
     *
     * @param Options $option
     *
     * @return AdapterInterface
     */
    public function setOptions(Options $option);

    /**
     * Opens an archive
     *
     * @param String $path The path to the archive
     *
     * @return ArchiveInterface
     *
     * @throws InvalidArgumentException In case the provided path is not valid
     * @throws RuntimeException         In case of failure
     */
    public function open($path);

    /**
     * Creates a new archive
     *
     * @param String                         $path      The path to the archive
     * @param String|Array|\Traversable|null $files     A filename, an array of files, or a \Traversable instance
     * @param Boolean                        $recursive Recurse into directories
     *
     * @return ArchiveInterface
     *
     * @throws RuntimeException      In case of failure
     * @throws NotSupportedException In case the archive can not be created without at least one file
     */
    public function create($path, $files = null, $recursive = true);

    /**
     * Tests adapter support for current environment
     *
     * @return Boolean
     */
    public function isSupported();

    /**
     * Returns the list of all archive members
     *
     * @param String $path The path to the archive
     *
     * @return Array
     *
     * @throws RuntimeException In case of failure
     */
    public function listMembers($path);

    /**
     * Adds a file to the archive
     *
     * @param String                    $path  The path to the archive
     * @param String|Array|\Traversable $files A filename, an array of files, or a \Traversable instance
     *
     * @return Array
     *
     * @throws RuntimeException In case of failure
     */
    public function addFile($path, $files);
    
    /**
     * Returns the adapter name
     *
     * @return String
     */
    public static function getName();
}
