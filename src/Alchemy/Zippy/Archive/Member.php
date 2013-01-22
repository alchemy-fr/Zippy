<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Archive;

use Alchemy\Zippy\Adapter\AdapterInterface;

/**
 * Represents a member of an archive.
 */
class Member implements MemberInterface
{
    /**
     * The location of the file
     *
     * @var String
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
     * The path to the archive that contains the member
     *
     * @var String
     */
    private $archivePath;

    /**
     * An adapter
     *
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Constructor
     *
     * @param String           $archivePath      The path of the archive which contain the member
     * @param AdapterInterface $adapter          AdapterInterface    The archive adapter interface
     * @param String           $location         The path of the archive member
     * @param Integer          $fileSize         The uncompressed file size
     * @param \DateTime        $lastModifiedDate The last modifed date of the member
     * @param Boolean          $isDir            Tells wheteher the member is a directory or not
     */
    public function __construct($archivePath, AdapterInterface $adapter, $location, $fileSize, \DateTime $lastModifiedDate, $isDir)
    {
        $this->archivePath = $archivePath;
        $this->adapter = $adapter;
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

    /**
     * {@inheritdoc}
     */
    public function extract($to = null)
    {
        $this->adapter->extractMembers($this->archivePath, $this->location, $to);

        return new \SplFileInfo(sprintf('%s%s', rtrim(null === $to ? $this->archivePath : $to, '/'), $this->location));
    }
}
