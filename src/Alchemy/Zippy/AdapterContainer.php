<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy;

use Alchemy\Zippy\Adapter\ZipAdapter;
use Alchemy\Zippy\Adapter\GNUTarAdapter;

class AdapterContainer extends \Pimple
{
    /**
     * Builds the adapter container
     *
     * @return AdapterContainer
     */
    public static function load()
    {
        $container = new static();

        $container['zip.inflator'] = null;
        $container['zip.deflator'] = null;

        $container['Alchemy\\Zippy\\Adapter\\ZipAdapter'] = $container->share(function ($container) {
            return ZipAdapter::newInstance($container['zip.inflator'], $container['zip.deflator']);
        });

        $container['gnu-tar.inflator'] = null;
        $container['gnu-tar.deflator'] = null;

        $container['Alchemy\\Zippy\\Adapter\\GNUTarAdapter'] = $container->share(function ($container) {
            return GNUTarAdapter::newInstance($container['gnu-tar.inflator'], $container['gnu-tar.deflator']);
        });

        return $container;
    }
}
