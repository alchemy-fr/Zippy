<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Resource\Reader\Http;

use Alchemy\Zippy\Resource\Resource as ZippyResource;
use Alchemy\Zippy\Resource\ResourceReader;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientReader implements ResourceReader
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var ZippyResource
     */
    private $resource;

    public function __construct(ZippyResource $resource, HttpClientInterface $client)
    {
        $this->resource = $resource;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->buildRequest()->getContent();
    }

    /**
     * @return resource
     */
    public function getContentsAsStream()
    {
        $stream = fopen('php://temp', 'r+');

        foreach ($this->client->stream($this->buildRequest()) as $chunk) {
            fwrite($stream, $chunk->getContent());
        }

        fseek($stream, 0);

        return $stream;
    }

    /**
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     */
    private function buildRequest()
    {
        return $this->client->request('GET', $this->resource->getOriginal());
    }
}
