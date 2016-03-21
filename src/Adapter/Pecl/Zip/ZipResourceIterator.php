<?php

namespace Alchemy\Zippy\Adapter\Pecl\Zip;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIterator;
use Alchemy\Resource\Reader\RawStreamReader;
use Alchemy\Resource\ResourceReader;
use Alchemy\Resource\ResourceReaderResolver;
use Alchemy\Resource\ResourceUri;

class ZipResourceIterator implements PackagedResourceIterator, ResourceReaderResolver
{

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
     * @param PackagedResource $parent
     */
    public function __construct(PackagedResource $parent) {
        $this->parent = $parent;

        $this->archive = new \ZipArchive();
        $this->archive->open($this->parent->getAbsoluteUri()->getResource());
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

        return new PackagedResource(
            ResourceUri::fromString($file),
            $this,
            $this->parent->getWriterResolver(),
            $this->parent
        );
    }

    /**
     * Resolves a reader for the given resource URI.
     *
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function resolveReader(ResourceUri $resource)
    {
        return new RawStreamReader($this->archive->getStream($resource->getResource()));
    }
}
