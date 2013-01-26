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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException as SfIOException;

/**
 * This class transport an object using the local filesystem
 */
class LocalTeleporter implements TeleporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function teleport(Resource $resource, $context)
    {
        $filesytem = new Filesystem();

        try {
            if (is_file($resource->getOriginal())) {
                $filesytem->copy($resource->getOriginal(), $context.$resource->getTarget());
            } elseif (is_dir($resource->getOriginal())) {
                $filesytem->mirror($resource->getOriginal(), $context.$resource->getTarget(), true);
            }
        } catch (SfIOException $e) {
            throw new IOException(sprintf('Could not write %s', $context.$resource->getTarget()), $e->getCode(), $e);
        }

        throw new InvalidArgumentException(sprintf('%s must be a file or a directory', $resource->getOriginal()));
    }
}
