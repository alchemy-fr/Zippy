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

    public function __construct($location, AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->location = $location;
    }

    /**
     * Counts all the archives members
     *
     * @return int
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }

      /**
     * Returns an Iterator for the current archive
     *
     * This method implements the IteratorAggregate interface.
     *
     * @return \Iterator An iterator
     *
     * @throws LogicException if the in() method has not been called
     */
    public function getIterator()
    {
        if (0 === count($this->members)) {
            throw new LogicException('You must call members() method before iterating over a Finder.');
        }

        return new \ArrayIterator($this->members);
    }

    /**
     * @inheritdoc
     */
    public function members()
    {
        $this->members = array_map(function($filename) {
            return new File($filename);
        }, $this->adapter->listMembers($this->location));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function add($sources)
    {
        foreach ($this->adapter->addFile($this->location, $sources) as $file) {
            $this->members[] = new File($file->getRealPath());
        }
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
