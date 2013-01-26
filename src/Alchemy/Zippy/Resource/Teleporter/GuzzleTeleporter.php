<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Resource\Teleporter;

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Exception\IOException;
use Guzzle\Http\Client;
use Guzzle\Plugin\Backoff\BackoffPlugin;
use Guzzle\Common\Event;

/**
 * This class transport an object using the HTTP protocol
 */
class GuzzleTeleporter implements TeleporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function teleport($from, $to)
    {
        $client = new Client();

        $client->getEventDispatcher()->addListener('request.error', function(Event $event) {
            // override guzzle default behavior of throwing exceptions when 4xx & 5xx responses are encountered
            $event->stopPropagation();
        }, -254);

        // Use a static factory method to get a backoff plugin using the exponential backoff strategy
        $backoffPlugin = BackoffPlugin::getExponentialBackoff(3, array(500, 503, 408));
        // Add the backoff plugin to the client object
        $client->addSubscriber($backoffPlugin);

        $response = $client->get($from)->send();

        if (!$response->isSuccessful()) {
            throw new InvalidArgumentException('provided resource URI is not valid');
        }

        $response->getBody()->seek(0);

        if (false === file_put_contents($to, $response->getBody())) {
            throw new IOException(sprintf('Could not write %s', $to));
        }
    }
}
