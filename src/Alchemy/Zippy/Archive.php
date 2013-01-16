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

use Alchemy\Zippy\Adapter\AdapterInterface;

/**
 * Represents an archive
 */
class Archive implements ArchiveInterface, \IteratorAggregate, \Countable
{
    /**
     * The path to the archive
     *
     * @var String
     */
    protected $location;

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
     * Constructor
     *
     * @param String           $location Path to the archive
     * @param AdapterInterface $adapter  An archive adapter
     */
    public function __construct($location, AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->location = $location;
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
        return $this->members = $this->adapter->listMembers($this->location);
    }

    /**
     * @inheritdoc
     */
    public function addMembers($sources, $recursive = true)
    {
        $this->adapter->add($this->location, $sources, $recursive);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeMembers($sources)
    {
        $this->adapter->remove($this->location, $sources);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->location;
    }
}
