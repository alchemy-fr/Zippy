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

abstract class AbstractTeleporter implements TeleporterInterface
{
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
