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
use Alchemy\Zippy\File;
use Alchemy\Zippy\FileInterface;

class Archive implements ArchiveInterface, \IteratorAggregate, \Countable
{
    /**
     * The path to the archive
     *
     * @var string
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
     * @param String            $location   Path to the archive
     * @param AdapterInterface  $adapter    An archiveAdapter 
     */
    public function __construct($location, AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->location = $location;
    }

    /**
     * Counts all the archives members
     *
     * @return Integer
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
     *
     * @throws LogicException if the in() method has not been called
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
        return $this->members = array_map(function($filename) {
            return new File($filename);
        }, $this->adapter->listMembers($this->location));
    }

    /**
     * @inheritdoc
     */
    public function add($sources)
    {
        $this->adapter->addFile($this->location, $sources);
    }

    /**
     * @inheritdoc
     */
    public function addDirectory($source, $target = null, $recursive = true)
    {

    }

    /**
     * @inheritdoc
     */
    public function remove(FileInterface $file)
    {

    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->location;
    }
}
