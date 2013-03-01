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

interface TeleporterInterface
{
    /**
     * Teleport a file from a destination to an other
     *
     * @param String|Resource $from A remote or local file or resource
     * @param String          $to   A local path
     *
     * @throw IOException In case file could not be written on local
     * @throw InvalidArgumentException In case path to file is not valid
     */
    public function teleport(Resource $resource, $context);
}
