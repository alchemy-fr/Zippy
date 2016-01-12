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

abstract class AbstractTeleporter implements TeleporterInterface
{
    /**
     * Writes the target
     *
     * @param string $data
     * @param \Alchemy\Zippy\Resource\Resource $resource
     * @param string $context
     *
     * @return TeleporterInterface
     *
     * @throws IOException
     */
    protected function writeTarget($data, Resource $resource, $context)
    {
        $target = $this->getTarget($context, $resource);

        if (! file_exists(dirname($target)) && false === mkdir(dirname($target))) {
            throw new IOException(sprintf('Could not create parent directory %s', dirname($target)));
        }

        if (false === file_put_contents($target, $data)) {
            throw new IOException(sprintf('Could not write to %s', $target));
        }

        return $this;
    }

    /**
     * Returns the relative target of a Resource
     *
     * @param String   $context
     * @param Resource $resource
     *
     * @return String
     */
    protected function getTarget($context, Resource $resource)
    {
        return sprintf('%s/%s', rtrim($context, '/'), $resource->getTarget());
    }
}
