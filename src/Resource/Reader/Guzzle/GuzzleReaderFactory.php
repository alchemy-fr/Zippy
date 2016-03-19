<?php

namespace Alchemy\Zippy\Resource\Reader\Guzzle;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderFactory;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class GuzzleReaderFactory implements ResourceReaderFactory
{
    /**
     * @var ClientInterface|null
     */
    private $client = null;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client;

        if (! $this->client) {
            $this->client = new Client();
        }
    }

    /**
     * @param \Alchemy\Zippy\Resource\Resource $resource
     * @param string $context
     * @return ResourceReader
     */
    public function getReader(Resource $resource, $context)
    {
        return new GuzzleReader($resource, $this->client);
    }
}
