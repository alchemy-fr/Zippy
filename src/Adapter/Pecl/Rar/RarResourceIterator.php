<?php

namespace Alchemy\Zippy\Adapter\Pecl\Rar;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIterator;
use Alchemy\Zippy\Resource\ResourceUri;
use RecursiveDirectoryIterator;

class RarResourceIterator extends \RecursiveDirectoryIterator implements PackagedResourceIterator
{
    /**
     * @var string
     */
    private $root;

    /**
     * @var PackagedResource
     */
    private $resource;

    /**
     * @var PackagedResource[]
     */
    private $resources = [];

    /**
     * @var ResourceUri
     */
    private $parent;

    /**
     * @param ResourceUri $resource
     * @param PackagedResource $parent
     * @param int $flags
     */
    public function __construct(ResourceUri $resource, PackagedResource $parent = null, $flags = null)
    {
        $this->root = substr($resource, 0, strpos($resource, '#'));
        $this->parent = $parent;
        $this->resource = $resource;

        parent::__construct($resource, $flags);
    }

    /**
     * @return PackagedResource
     */
    public function current()
    {
        /** @var \SplFileInfo $currentFile */
        $currentFile = parent::current();
        $current = $currentFile->getBasename();

        echo '*it : ' . $current . PHP_EOL;

        if (! isset($this->resources[$current])) {
            $this->resources[$current] = new PackagedResource(
                ResourceUri::fromString($current),
                $this->parent
            );
        }

        echo '*pr : ' . $this->resources[$current]->getAbsoluteUri();

        return $this->resources[$current];
    }

    public function getChildren()
    {
        return new self($this->resource, $this->current(), $this->getFlags());
    }

    /**
     * @return \RecursiveIteratorIterator
     */
    public function getIterator()
    {
        return $this;
    }

    /**
     * @param PackagedResource $parent
     * @return RarResourceIterator
     */
    public function withParent(PackagedResource $parent)
    {
        return new self($this->resource, $parent, $this->getFlags());
    }
}
