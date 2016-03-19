<?php

namespace Alchemy\Zippy\Resource\Writer;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceWriter;

class StreamWriter implements ResourceWriter
{
    /**
     * @param ResourceReader $reader
     * @param string $target
     */
    public function writeFromReader(ResourceReader $reader, $target)
    {
        $targetResource = fopen($target, 'w+');
        $sourceResource = $reader->getContentsAsStream();

        stream_copy_to_stream($sourceResource, $targetResource);
        fclose($targetResource);
    }
}
