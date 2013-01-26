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

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Exception\IOException;
use Alchemy\Zippy\Exception\InvalidArgumentException;

/**
 * This class transport an object using php stream wrapper
 */
class StreamTeleporter implements TeleporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function teleport(Resource $resource, $context)
    {
        $from = $resource->getOriginal();
        $to = $context . $resource->getTarget();

        if (is_resource($from)) {
            $stream = $from;
        } else {
            $url = $from;
        }

        if (null === $stream) {
            $stream = fopen($url, 'rb');

            if (!is_resource($stream)) {
                throw new InvalidArgumentException(sprintf('The stream or file "%s" could not be opened: ', $url));
            }
        }

        $content = stream_get_contents($stream);

        fclose($stream);

        if (false === file_put_contents($to, $content)) {
            throw new IOException(sprintf('Could not write %s', $to));
        }
    }
}
