<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Archive\Archive;
use Alchemy\Zippy\Adapter\Resource\FileResource;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @inheritdoc
     */
    public function open($path)
    {
        return new Archive(new FileResource($path), $this);
    }
}
