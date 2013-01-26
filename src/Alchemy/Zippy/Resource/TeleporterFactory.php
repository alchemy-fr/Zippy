<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Resource;

use Alchemy\Zippy\Exception\InvalidArgumentException;
use Alchemy\Zippy\Resource\Teleporter\GuzzleTeleporter;
use Alchemy\Zippy\Resource\Teleporter\LocalTeleporter;
use Alchemy\Zippy\Resource\Teleporter\StreamTeleporter;
use Alchemy\Zippy\Resource\Teleporter\TeleporterInterface;

class TeleporterFactory
{
    /**
     * Maps the transport protocole to the proper teleporter
     *
     * @param   $protocole A protocole scheme
     *
     * @return TeleporterInterface
     *
     * @throws InvalidArgumentException In case no teleporter were found
     */
    public static function create($protocole)
    {
        switch ($protocole) {
            case 'http':
                return new GuzzleTeleporter();
                break;
            case 'file':
                return new LocalTeleporter();
                break;
            case 'local' :
            case 'ftp' :
                return new StreamTeleporter();
            default:
                throw new InvalidArgumentException(sprintf('No teleporter available for %s protocole', $protocole));
                break;
        }
    }
}
