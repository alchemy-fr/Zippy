<?php

namespace Alchemy\Zippy\Adapter\Guzzle;

use Alchemy\Resource\ResourceReader;
use Alchemy\Resource\ResourceReaderFactory;
use Alchemy\Resource\ResourceUri;
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
