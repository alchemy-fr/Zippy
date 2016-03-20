<?php

namespace Alchemy\Zippy\Package\Iterator;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIterator;

class FilteredPackagedResourceIterator implements PackagedResourceIterator
{
    /**
     * @var PackagedResourceIterator
     */
    private $iterator;

    private $filter;

    public function __construct(PackagedResourceIterator $iterator, callable $filter)
    {
        $this->iterator = $iterator;
        $this->filter = $filter;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        do {
            $this->iterator->next();
        } while ($this->shouldSkipCurrent($this->filter));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iterator->key();
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
        while ($this->shouldSkipCurrent($this->filter)) {
            $this->iterator->next();
        }

        return $this->iterator->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iterator->rewind();

        while ($this->shouldSkipCurrent($this->filter)) {
            $this->iterator->next();
        }
    }

    /**
     * @return PackagedResource
     */
    public function current()
    {
        while ($this->shouldSkipCurrent($this->filter)) {
            $this->iterator->next();
        }

        return $this->iterator->current();
    }

    /**
     * @param $filter
     * @return bool
     */
    private function shouldSkipCurrent($filter)
    {
        return $this->iterator->valid() && !$filter($this->iterator->current());
    }
}
