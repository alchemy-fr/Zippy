<?php

namespace Alchemy\Zippy\Iterator;

use ArrayIterator;

class MappingArrayIterator extends \ArrayIterator
{

    /**
     * @var callable
     */
    private $mapFunction;

    /**
     * @param array $items
     * @param callable $mapFunction
     */
    public function __construct(array $items, callable $mapFunction)
    {
        $this->mapFunction = $mapFunction;

        parent::__construct($items);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return call_user_func($this->mapFunction, parent::current());
    }
}
