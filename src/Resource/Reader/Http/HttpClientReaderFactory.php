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
use Alchemy\Zippy\Resource\ResourceReaderFactory;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientReaderFactory implements ResourceReaderFactory
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        $this->client = $client ?: HttpClient::create();
    }

    /**
     * @param ZippyResource $resource
     * @param string        $context
     *
     * @return ResourceReader
     */
    public function getReader(ZippyResource $resource, $context)
    {
        return new HttpClientReader($resource, $this->client);
    }
}
