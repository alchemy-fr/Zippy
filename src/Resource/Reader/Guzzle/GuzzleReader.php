<?php

namespace Alchemy\Zippy\Resource\Reader\Guzzle;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceUri;
use GuzzleHttp\ClientInterface;

class GuzzleReader implements ResourceReader
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var \Alchemy\Zippy\Resource\Resource
     */
    private $resource;

    /**
     * @param ResourceUri $resource
     * @param ClientInterface $client
     */
    public function __construct(ResourceUri $resource, ClientInterface $client = null)
    {
        $this->resource = $resource;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->buildRequest()->getBody()->getContents();
    }

    /**
     * @return resource
     */
    public function getContentsAsStream()
    {
        $response = $this->buildRequest()->getBody()->getContents();
        $stream = fopen('php://temp', 'r+');

        if ($response != '') {
            fwrite($stream, $response);
            fseek($stream, 0);
        }

        return $stream;
    }

    private function buildRequest()
    {
        return $this->client->request('GET', $this->resource->getUri());
    }
}
