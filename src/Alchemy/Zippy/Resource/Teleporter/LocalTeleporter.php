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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException as SfIOException;
use Alchemy\Zippy\Exception\IOException;
use Alchemy\Zippy\Exception\InvalidArgumentException;

/**
 * This class transport an object using the local filesystem
 */
class LocalTeleporter implements TeleporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function teleport($from, $to)
    {
        $filesytem = new Filesystem();

        try {
            if (is_file($from)) {
                $filesytem->copy($from, $to);
            } elseif (is_dir($from)) {
                $filesytem->mirror($from, $to, true);
            }
        } catch (SfIOException $e) {
            throw new IOException(sprintf('Could not write %s', $to), $e->getCode(), $e);
        }

        throw new InvalidArgumentException(sprintf('%s must be a file or a directory', $from));
    }
}
