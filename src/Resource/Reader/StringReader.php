<?php

namespace Alchemy\Zippy\Resource\Reader;

use Alchemy\Zippy\Resource\ResourceReader;

class StringReader implements ResourceReader
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @param string $contents
     */
    public function __construct($contents)
    {
        $this->contents = (string) $contents;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return resource
     */
    public function getContentsAsStream()
    {
        $stream = fopen('php://temp', 'rw');

        fwrite($stream, $this->contents);
        fseek($stream, 0);

        return $stream;
    }
}
