<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy;

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\FileInterface;

interface ArchiveInterface
{
    /**
     * Adds a file into the archive
     *
     * @param String|\SplFileInfo $source The path to the file
     *
     * @return ArchiveInterface
     *
     * @throws InvalidArgumentException In case the provided source path is not valid
     * @throws RuntimeException         In case of failure
     */
    public function add($source);

    /**
     * Adds a directory into the archive

     * @param String|\SplFileInfo $source    The path to the directory
     * @param String|null         $target    The directory file path, null to use the same as source
     * @param Boolean             $recursive Recurse into directories
     *
     * @return ArchiveInterface
     *
     * @throws InvalidArgumentException In case the provided source path is not valid
     * @throws RuntimeException         In case of failure
     */
    public function addDirectory($source, $target = null, $recursive = true);

    /**
     * Removes a file from the archive
     *
     * @param FileInterface $file The file to remove
     *
     * @return ArchiveInterface
     *
     * @throws RuntimeException In case of failure
     */
    public function remove(FileInterface $file);
}
