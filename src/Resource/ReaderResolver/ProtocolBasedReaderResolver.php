<?php

namespace Alchemy\Zippy\Resource\ReaderResolver;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderFactory;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class ProtocolBasedReaderResolver implements ResourceReaderResolver
{
    /**
     * @var ResourceReaderFactory[]
     */
    private $factories = [];

    /**
     * @var int[] Dictionary of factory indexes, indexed by resource protocol name
     */
    private $protocolFactoryIndexes = [];

    /**
     * @param ResourceReaderFactory $factory
     * @param string|string[] $protocols List of compatible protocols
     */
    public function addFactory(ResourceReaderFactory $factory, $protocols)
    {
        $protocols = is_array($protocols) ? $protocols : [ $protocols ];
        $index = count($this->factories);

        $this->factories[$index] = $factory;

        foreach ($protocols as $protocol) {
            $this->protocolFactoryIndexes[$protocol] = $index;
        }
    }

    /**
     * Resolves a reader for the given resource URI.
     *
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function resolveReader(ResourceUri $resource)
    {
        if (! array_key_exists($resource->getProtocol(), $this->protocolFactoryIndexes)) {
            throw new \RuntimeException('Unsupported protocol: ' . $resource->getProtocol() . '( ' . $resource  . ')');
        }

        $index = $this->protocolFactoryIndexes[$resource->getProtocol()];
        $factory = $this->factories[$index];

        return $factory->getReader($resource);
    }
}
