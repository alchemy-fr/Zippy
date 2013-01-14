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

/**
 * Represents a member of an archive.
 */
class Member implements MemberInterface
{
    /**
     * The location of the file
     *
     * @var     String
     */
    private $location;

    /**
     * Tells whether the archive member is a directory or not
     *
     * @var Boolean
     */
    private $isDir;

    /**
     * The uncompressed size of the file
     *
     * @var Integer
     */
    private $size;

    /**
     * The last modified date of the file
     *
     * @var \DateTime
     */
    private $lastModifiedDate;

    /**
     * Constructor
     *
     * @param String    $location         The path of the archive member
     * @param Integer   $fileSize         The uncompressed file size
     * @param \DateTime $lastModifiedDate The last modifed date of the member
     * @param Boolean   $isDir            Tells wheteher the member is a directory or not
     */
    public function __construct($location, $fileSize, \DateTime $lastModifiedDate, $isDir = false)
    {
        $this->location = $location;
        $this->isDir = $isDir;
        $this->size = $fileSize;
        $this->lastModifiedDate = $lastModifiedDate;
    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @inheritdoc
     */
    public function isDir()
    {
        return $this->isDir;
    }

    /**
     * @inheritdoc
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->location;
    }
}
