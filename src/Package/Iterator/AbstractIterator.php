<?php

namespace Alchemy\Zippy\Package\Iterator;

use Alchemy\Zippy\Package\PackagedResourceIterator;

abstract class AbstractIterator implements PackagedResourceIterator
{

    /**
     * @var \Iterator|null
     */
    private $iterator;

    /**
     * @return \Iterator
     */
    abstract protected function buildIterator();

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        if ($this->iterator === null) {
            $this->iterator = $this->buildIterator();
        }

        return $this->iterator;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->getIterator()->next();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->getIterator()->key();
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
        return $this->getIterator()->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->getIterator()->rewind();
    }


}
