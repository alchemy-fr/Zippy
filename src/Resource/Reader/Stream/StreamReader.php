<?php

namespace Alchemy\Zippy\Resource\Reader\Stream;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\ResourceReader;

class StreamReader implements ResourceReader
{
    /**
     * @var \Alchemy\Zippy\Resource\Resource
     */
    private $resource;

    /**
     * @param \Alchemy\Zippy\Resource\Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return file_get_contents($this->resource->getOriginal());
    }

    /**
     * @return resource
     */
    public function getContentsAsStream()
    {
        $stream = is_resource($this->resource->getOriginal()) ?
            $this->resource->getOriginal() : @fopen($this->resource->getOriginal(), 'rb');

        return $stream;
    }
}
