<?php

namespace Alchemy\Zippy\Adapter\Pecl\Zip;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIterator;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class ZipResourceIterator implements PackagedResourceIterator
{

    /**
     * @var ResourceUri
     */
    private $resource;

    /**
     * @var \ZipArchive
     */
    private $archive;

    /**
     * @var int
     */
    private $current = 0;

    /**
     * @var PackagedResource
     */
    private $parent;

    /**
     * @var ResourceReaderResolver
     */
    private $readerResolver;

    /**
     * @param ResourceUri $resourceUri
     * @param PackagedResource $parent
     */
    public function __construct(
        ResourceUri $resourceUri,
        PackagedResource $parent = null
    ) {
        $this->archive = new \ZipArchive();

        $this->parent = $parent;
        $this->resource = $resourceUri;

        $this->archive->open($resourceUri->getResource());
        $this->readerResolver = new ZipResourceReaderResolver($this->archive);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->archive->getNameIndex($this->current);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->current < $this->archive->numFiles;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->current = 0;
    }

    /**
     * @return PackagedResource
     */
    public function current()
    {
        $file = $this->key();

        return new PackagedResource(ResourceUri::fromString($file), $this->readerResolver, $this->parent);
    }

    /**
     * @param PackagedResource $parent
     * @return PackagedResourceIterator
     */
    public function withParent(PackagedResource $parent)
    {
        $iterator = clone $this;
        $iterator->parent = $parent;

        return $iterator;
    }
}
