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
use Alchemy\Zippy\Adapter\Resource\FileResource;
use Alchemy\Zippy\Adapter\Resource\ResourceInterface;

/**
 * Represents an archive
 */
class Archive implements ArchiveInterface
{
    /**
     * The path to the archive
     *
     * @var String
     */
    protected $path;

    /**
     * The archive adapter
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * An array of archive members
     *
     * @var Array
     */
    protected $members = array();

    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * Constructor
     *
     * @param String            $path     Path to the archive
     * @param AdapterInterface  $adapter  An archive adapter
     * @param ResourceInterface $resource A resource
     */
    public function __construct($path, AdapterInterface $adapter, ResourceInterface $resource)
    {
        $this->adapter = $adapter;
        $this->path = $path;
        $this->resource = $resource ?: new FileResource($resource);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->getMembers());
    }

    /**
     * Returns an Iterator for the current archive
     *
     * This method implements the IteratorAggregate interface.
     *
     * @return \ArrayIterator An iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getMembers());
    }

    /**
     * @inheritdoc
     */
    public function getMembers()
    {
        return $this->members = $this->adapter->listMembers($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function addMembers($sources, $recursive = true)
    {
        $this->adapter->add($this->resource, $sources, $recursive);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeMembers($sources)
    {
        $this->adapter->remove($this->resource, $sources);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
     public function extract($to)
     {
        $this->adapter->extract($this->resource, $to);

        return $this;
     }

    /**
     * @inheritdoc
     */
    public function extractMembers($members)
    {
        $this->adapter->extractMembers($this->resource, $members);

        return $this;
    }
}
