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

use Alchemy\Zippy\Exception\RuntimeException;

class FileInterface
{
    /**
     * Gets the location of a file
     *
     * @return String
     */
    public function getLocation();

    /**
     * Extracts a file from the archive to the given path
     *
     * @throws RuntimeException In case the extraction failed
     */
    public function extract($target);

    /**
     * Tells whether the current file is a directory or not
     *
     * @return Boolean
     */
    public function isDir();

    /**
     * Relocates a file with the given path
     *
     * @return FileInterface
     *
     * @throws RuntimeException In case of failure
     */
    public function rename($location);

    /**
     * @inheritdoc
     */
    public function __toString();
}
