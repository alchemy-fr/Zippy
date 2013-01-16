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

interface MemberInterface
{
    /**
     * Gets the location of an archive member
     *
     * @return String
     */
    public function getLocation();

    /**
     * Tells whether the member is a directory or not
     *
     * @return Boolean
     */
    public function isDir();

    /*
     * Returns the last modified date of the member
     *
     * @return \DateTime
     */
    public function getLastModifiedDate();

    /**
     * Returns the (uncompressed) size of the member
     *
     * If the size is unknown, returns -1
     *
     * @return Integer
     */
    public function getSize();

    /**
     * @inheritdoc
     */
    public function __toString();
}
