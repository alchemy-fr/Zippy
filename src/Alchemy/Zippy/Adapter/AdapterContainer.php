<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Adapter\ZipAdapter;
use Alchemy\Zippy\Adapter\GNUTar\TarGNUTarAdapter;
use Alchemy\Zippy\Adapter\GNUTar\TarGzGNUTarAdapter;
use Alchemy\Zippy\Adapter\GNUTar\TarBz2GNUTarAdapter;

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

        $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarGNUTarAdapter'] = $container->share(function ($container) {
            return TarGNUTarAdapter::newInstance($container['gnu-tar.inflator'], $container['gnu-tar.deflator']);
        });

        $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarGzGNUTarAdapter'] = $container->share(function ($container) {
            return TarGzGNUTarAdapter::newInstance($container['gnu-tar.inflator'], $container['gnu-tar.deflator']);
        });

        $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarBz2GNUTarAdapter'] = $container->share(function ($container) {
            return TarBz2GNUTarAdapter::newInstance($container['gnu-tar.inflator'], $container['gnu-tar.deflator']);
        });

        return $container;
    }
}
