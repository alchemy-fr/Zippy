<?php

namespace Alchemy\Zippy\Resource\Reader;

use Alchemy\Zippy\Resource\ResourceReader;

class RawStreamReader implements ResourceReader
{

    private $stream;

    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('Invalid resource.');
        }

        $this->stream = $resource;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return stream_get_contents($this->stream);
    }

    /**
     * @return resource
     */
    public function getContentsAsStream()
    {
        return $this->stream;
    }
}
