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
    protected function writeTarget($data, Resource $resource, $context)
    {
        $target = $this->getTarget($context, $resource);

        if (false === file_put_contents($target, $data)) {
            throw new IOException(sprintf('Could not write to %s', $target));
        }

        return $this;
    }

    protected function getTarget($context, Resource $resource)
    {
        return sprintf('%s/%s', rtrim($context, '/'), $resource->getTarget());
    }
}
