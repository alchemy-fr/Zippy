<?php

namespace Alchemy\Zippy\Resource\Reader;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceUri;

class StreamReader implements ResourceReader
{
    /**
     * @var ResourceUri
     */
    private $resource;

    /**
     * @var resource[]
     */
    private $streams = [];

    /**
     * @param ResourceUri $resource
     */
    public function __construct(ResourceUri $resource)
    {
        $this->resource = $resource;
    }

    public function __destruct()
    {
        foreach ($this->streams as $stream) {
            @fclose($stream);
        }
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return stream_get_contents($this->getContentsAsStream());
    }

    /**
     * @return resource
     */
    public function getContentsAsStream()
    {
        $stream = @fopen(rawurldecode($this->resource), 'r');

        if ($stream === false) {
            throw new \RuntimeException('Unable to open stream resource for ' . rawurldecode($this->resource));
        }

        $this->streams[] = $stream;

        return $stream;
    }
}
