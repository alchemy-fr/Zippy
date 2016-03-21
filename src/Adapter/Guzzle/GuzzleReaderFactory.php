<?php

namespace Alchemy\Zippy\Adapter\Guzzle;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderFactory;
use Alchemy\Zippy\Resource\ResourceUri;
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
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function createReaderFor(ResourceUri $resource)
    {
        return new GuzzleReader($resource, $this->client);
    }
}
