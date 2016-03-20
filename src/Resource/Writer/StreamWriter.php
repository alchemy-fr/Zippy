<?php

namespace Alchemy\Zippy\Resource\Writer;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceUri;
use Alchemy\Zippy\Resource\ResourceWriter;

class StreamWriter implements ResourceWriter
{
    /**
     * @param ResourceReader $reader
     * @param ResourceUri $target
     */
    public function writeFromReader(ResourceReader $reader, ResourceUri $target)
    {
        $targetResource = @fopen(rawurldecode($target->getResource()), 'w+');

        if ($targetResource === false) {
            throw new \RuntimeException('Unable to open ' . $target->getUri() . ' for writing');
        }

        $sourceResource = $reader->getContentsAsStream();

        if (@stream_copy_to_stream($sourceResource, $targetResource) === 0) {
            throw new \RuntimeException('Unable to write to ' . $target->getUri());
        };

        fclose($targetResource);
    }
}
